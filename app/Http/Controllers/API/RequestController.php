<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Request;
use App\Models\RequestAttachment;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Storage;

class RequestController extends Controller
{
    public function index(HttpRequest $request)
    {
        $user = $request->user();

        $requests = Request::where('user_id', $user->id)
            ->with(['currentDepartment', 'workflowPath', 'attachments'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'requests' => $requests
        ]);
    }

    public function store(HttpRequest $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $userRequest = Request::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'user_id' => $request->user()->id,
            'status' => 'draft',
        ]);

        return response()->json([
            'message' => 'Request created successfully',
            'request' => $userRequest->load(['currentDepartment', 'workflowPath'])
        ], 201);
    }

    public function show($id, HttpRequest $request)
    {
        $userRequest = Request::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with(['currentDepartment', 'workflowPath', 'attachments', 'transitions.actionedBy', 'transitions.toDepartment'])
            ->firstOrFail();

        return response()->json([
            'request' => $userRequest
        ]);
    }

    public function update($id, HttpRequest $request)
    {
        $userRequest = Request::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->whereIn('status', ['draft', 'need_more_details'])
            ->firstOrFail();

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'additional_details' => 'sometimes|nullable|string',
        ]);

        $userRequest->update($validated);

        return response()->json([
            'message' => 'Request updated successfully',
            'request' => $userRequest->load(['currentDepartment', 'workflowPath'])
        ]);
    }

    public function destroy($id, HttpRequest $request)
    {
        $userRequest = Request::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->where('status', 'draft')
            ->firstOrFail();

        $userRequest->delete();

        return response()->json([
            'message' => 'Request deleted successfully'
        ]);
    }

    public function submit($id, HttpRequest $request)
    {
        $userRequest = Request::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->whereIn('status', ['draft', 'need_more_details'])
            ->firstOrFail();

        // Get Department A
        $departmentA = \App\Models\Department::where('is_department_a', true)->first();

        if (!$departmentA) {
            return response()->json([
                'message' => 'Department A not found. Please contact administrator.'
            ], 500);
        }

        $userRequest->update([
            'current_department_id' => $departmentA->id,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        // Create transition record
        \App\Models\RequestTransition::create([
            'request_id' => $userRequest->id,
            'to_department_id' => $departmentA->id,
            'actioned_by' => $request->user()->id,
            'action' => 'submit',
            'from_status' => 'draft',
            'to_status' => 'pending',
            'comments' => 'Request submitted for review',
        ]);

        return response()->json([
            'message' => 'Request submitted successfully',
            'request' => $userRequest->load(['currentDepartment', 'workflowPath'])
        ]);
    }

    public function uploadAttachment($id, HttpRequest $request)
    {
        $userRequest = Request::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $file = $request->file('file');
        $path = $file->store('attachments', 'public');

        $attachment = RequestAttachment::create([
            'request_id' => $userRequest->id,
            'uploaded_by' => $request->user()->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return response()->json([
            'message' => 'File uploaded successfully',
            'attachment' => $attachment
        ], 201);
    }

    public function deleteAttachment($requestId, $attachmentId, HttpRequest $request)
    {
        $attachment = RequestAttachment::where('id', $attachmentId)
            ->where('request_id', $requestId)
            ->whereHas('request', function($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->firstOrFail();

        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return response()->json([
            'message' => 'Attachment deleted successfully'
        ]);
    }
}
