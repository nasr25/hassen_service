# Laravel + Vue.js Dynamic Workflow Application

## Project Overview
This application implements a dynamic workflow system where:
- Users submit requests with attachments
- Department A reviews and routes to one of 6 paths
- Each path has specific departments with managers/employees
- Requests flow through departments and return to Department A for validation
- Admin can configure workflow dynamically

## Database Schema Created

### Tables:
1. **users** - Extended with role (admin/manager/employee/user) and is_active
2. **departments** - Stores all departments, Department A marked with `is_department_a` flag
3. **workflow_paths** - 6 configurable workflow paths
4. **workflow_path_steps** - Steps for each workflow path (which departments, order)
5. **department_user** - Many-to-many relationship (users belong to departments as manager/employee)
6. **requests** - User-submitted requests
7. **request_transitions** - History of all workflow transitions
8. **request_attachments** - File attachments for requests

## Implementation Steps

### Phase 1: Models (Already Created)
Models are in `app/Models/`:
- Department.php
- WorkflowPath.php
- WorkflowPathStep.php
- Request.php (Note: renamed from "Request" to avoid conflict)
- RequestTransition.php
- RequestAttachment.php

### Phase 2: Configure Models with Relationships

#### app/Models/User.php
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function departments()
    {
        return $this->belongsToMany(Department::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function submittedRequests()
    {
        return $this->hasMany(Request::class, 'user_id');
    }

    public function assignedRequests()
    {
        return $this->hasMany(Request::class, 'current_user_id');
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isManagerOf($departmentId)
    {
        return $this->departments()
            ->where('department_id', $departmentId)
            ->wherePivot('role', 'manager')
            ->exists();
    }

    public function isEmployeeOf($departmentId)
    {
        return $this->departments()
            ->where('department_id', $departmentId)
            ->exists();
    }
}
```

#### app/Models/Department.php
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'is_department_a',
        'is_active',
    ];

    protected $casts = [
        'is_department_a' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function managers()
    {
        return $this->belongsToMany(User::class)
            ->wherePivot('role', 'manager')
            ->withTimestamps();
    }

    public function employees()
    {
        return $this->belongsToMany(User::class)
            ->wherePivot('role', 'employee')
            ->withTimestamps();
    }

    public function workflowPathSteps()
    {
        return $this->hasMany(WorkflowPathStep::class);
    }

    public function currentRequests()
    {
        return $this->hasMany(Request::class, 'current_department_id');
    }
}
```

#### app/Models/WorkflowPath.php
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowPath extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'department_id',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function steps()
    {
        return $this->hasMany(WorkflowPathStep::class)->orderBy('step_order');
    }

    public function requests()
    {
        return $this->hasMany(Request::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
```

#### app/Models/WorkflowPathStep.php
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowPathStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'workflow_path_id',
        'department_id',
        'step_order',
        'requires_approval',
    ];

    protected $casts = [
        'requires_approval' => 'boolean',
    ];

    // Relationships
    public function workflowPath()
    {
        return $this->belongsTo(WorkflowPath::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function getNextStep()
    {
        return self::where('workflow_path_id', $this->workflow_path_id)
            ->where('step_order', '>', $this->step_order)
            ->orderBy('step_order', 'asc')
            ->first();
    }

    public function getPreviousStep()
    {
        return self::where('workflow_path_id', $this->workflow_path_id)
            ->where('step_order', '<', $this->step_order)
            ->orderBy('step_order', 'desc')
            ->first();
    }
}
```

#### app/Models/Request.php
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Request extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'user_id',
        'current_department_id',
        'current_user_id',
        'workflow_path_id',
        'status',
        'rejection_reason',
        'additional_details',
        'submitted_at',
        'completed_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function currentDepartment()
    {
        return $this->belongsTo(Department::class, 'current_department_id');
    }

    public function currentAssignee()
    {
        return $this->belongsTo(User::class, 'current_user_id');
    }

    public function workflowPath()
    {
        return $this->belongsTo(WorkflowPath::class);
    }

    public function attachments()
    {
        return $this->hasMany(RequestAttachment::class);
    }

    public function transitions()
    {
        return $this->hasMany(RequestTransition::class)->orderBy('created_at', 'desc');
    }

    // Helper methods
    public function getCurrentStep()
    {
        if (!$this->workflow_path_id || !$this->current_department_id) {
            return null;
        }

        return WorkflowPathStep::where('workflow_path_id', $this->workflow_path_id)
            ->where('department_id', $this->current_department_id)
            ->first();
    }
}
```

#### app/Models/RequestTransition.php
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestTransition extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'from_department_id',
        'to_department_id',
        'from_user_id',
        'to_user_id',
        'actioned_by',
        'action',
        'from_status',
        'to_status',
        'comments',
    ];

    // Relationships
    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    public function fromDepartment()
    {
        return $this->belongsTo(Department::class, 'from_department_id');
    }

    public function toDepartment()
    {
        return $this->belongsTo(Department::class, 'to_department_id');
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function actionedBy()
    {
        return $this->belongsTo(User::class, 'actioned_by');
    }
}
```

#### app/Models/RequestAttachment.php
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class RequestAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'uploaded_by',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    // Relationships
    public function request()
    {
        return $this->belongsTo(Request::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Helper methods
    public function getUrl()
    {
        return Storage::url($this->file_path);
    }

    public function delete()
    {
        Storage::delete($this->file_path);
        return parent::delete();
    }
}
```

### Phase 3: Workflow Engine Service

Create `app/Services/WorkflowEngine.php`:

```php
<?php

namespace App\Services;

use App\Models\Request;
use App\Models\RequestTransition;
use App\Models\WorkflowPathStep;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WorkflowEngine
{
    /**
     * Submit a new request
     */
    public function submitRequest(Request $request, $workflowPathId = null)
    {
        DB::beginTransaction();
        try {
            // Get Department A
            $departmentA = Department::where('is_department_a', true)->first();

            $request->update([
                'current_department_id' => $departmentA->id,
                'status' => 'pending',
                'submitted_at' => now(),
            ]);

            // Create transition record
            RequestTransition::create([
                'request_id' => $request->id,
                'to_department_id' => $departmentA->id,
                'actioned_by' => Auth::id(),
                'action' => 'submit',
                'from_status' => 'draft',
                'to_status' => 'pending',
                'comments' => 'Request submitted for review',
            ]);

            DB::commit();
            return ['success' => true, 'request' => $request];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Department A approves and assigns to a workflow path
     */
    public function assignToPath(Request $request, $workflowPathId)
    {
        DB::beginTransaction();
        try {
            // Get first step of the workflow path
            $firstStep = WorkflowPathStep::where('workflow_path_id', $workflowPathId)
                ->orderBy('step_order', 'asc')
                ->first();

            if (!$firstStep) {
                throw new \Exception('No steps defined for this workflow path');
            }

            $request->update([
                'workflow_path_id' => $workflowPathId,
                'current_department_id' => $firstStep->department_id,
                'status' => 'in_review',
            ]);

            RequestTransition::create([
                'request_id' => $request->id,
                'from_department_id' => $request->current_department_id,
                'to_department_id' => $firstStep->department_id,
                'actioned_by' => Auth::id(),
                'action' => 'assign',
                'from_status' => 'pending',
                'to_status' => 'in_review',
                'comments' => 'Assigned to workflow path',
            ]);

            DB::commit();
            return ['success' => true, 'request' => $request];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Move request to next step in workflow
     */
    public function moveToNextStep(Request $request, $comments = null)
    {
        DB::beginTransaction();
        try {
            $currentStep = $request->getCurrentStep();
            $nextStep = $currentStep?->getNextStep();

            // If no next step, return to Department A for validation
            $departmentA = Department::where('is_department_a', true)->first();
            $nextDepartmentId = $nextStep ? $nextStep->department_id : $departmentA->id;
            $newStatus = $nextStep ? 'in_review' : 'pending';

            $oldDepartmentId = $request->current_department_id;

            $request->update([
                'current_department_id' => $nextDepartmentId,
                'status' => $newStatus,
            ]);

            RequestTransition::create([
                'request_id' => $request->id,
                'from_department_id' => $oldDepartmentId,
                'to_department_id' => $nextDepartmentId,
                'actioned_by' => Auth::id(),
                'action' => 'approve',
                'from_status' => $request->status,
                'to_status' => $newStatus,
                'comments' => $comments ?? 'Moved to next step',
            ]);

            DB::commit();
            return ['success' => true, 'request' => $request];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Request more details from user
     */
    public function requestMoreDetails(Request $request, $comments)
    {
        DB::beginTransaction();
        try {
            $oldStatus = $request->status;
            $request->update([
                'status' => 'need_more_details',
                'rejection_reason' => $comments,
            ]);

            RequestTransition::create([
                'request_id' => $request->id,
                'from_department_id' => $request->current_department_id,
                'actioned_by' => Auth::id(),
                'action' => 'request_details',
                'from_status' => $oldStatus,
                'to_status' => 'need_more_details',
                'comments' => $comments,
            ]);

            DB::commit();
            return ['success' => true, 'request' => $request];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Reject request
     */
    public function rejectRequest(Request $request, $reason)
    {
        DB::beginTransaction();
        try {
            $request->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'completed_at' => now(),
            ]);

            RequestTransition::create([
                'request_id' => $request->id,
                'from_department_id' => $request->current_department_id,
                'actioned_by' => Auth::id(),
                'action' => 'reject',
                'from_status' => $request->status,
                'to_status' => 'rejected',
                'comments' => $reason,
            ]);

            DB::commit();
            return ['success' => true, 'request' => $request];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Complete request (from Department A final validation)
     */
    public function completeRequest(Request $request, $comments = null)
    {
        DB::beginTransaction();
        try {
            $request->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            RequestTransition::create([
                'request_id' => $request->id,
                'from_department_id' => $request->current_department_id,
                'actioned_by' => Auth::id(),
                'action' => 'complete',
                'from_status' => 'pending',
                'to_status' => 'completed',
                'comments' => $comments ?? 'Request completed successfully',
            ]);

            DB::commit();
            return ['success' => true, 'request' => $request];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Send back to specific department
     */
    public function sendBackToDepartment(Request $request, $departmentId, $comments)
    {
        DB::beginTransaction();
        try {
            $oldDepartmentId = $request->current_department_id;

            $request->update([
                'current_department_id' => $departmentId,
                'status' => 'in_review',
            ]);

            RequestTransition::create([
                'request_id' => $request->id,
                'from_department_id' => $oldDepartmentId,
                'to_department_id' => $departmentId,
                'actioned_by' => Auth::id(),
                'action' => 'request_details',
                'from_status' => $request->status,
                'to_status' => 'in_review',
                'comments' => $comments,
            ]);

            DB::commit();
            return ['success' => true, 'request' => $request];
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
```

### Phase 4: API Controllers

#### Create base controller
```bash
php artisan make:controller API/AuthController
php artisan make:controller API/RequestController
php artisan make:controller API/WorkflowController
php artisan make:controller API/DepartmentController
php artisan make:controller API/AdminController
```

## Next Steps

1. **Configure CORS** in `config/cors.php`
2. **Set up API routes** in `routes/api.php`
3. **Create Vue.js frontend** with Vite
4. **Implement file upload** handling
5. **Add notifications** system
6. **Create admin panel** for workflow configuration

## API Endpoints Structure

### User Endpoints
- POST /api/auth/register
- POST /api/auth/login
- POST /api/auth/logout
- GET /api/auth/user

### Request Endpoints
- GET /api/requests - List user's requests
- POST /api/requests - Create new request
- GET /api/requests/{id} - View request details
- POST /api/requests/{id}/submit - Submit for review
- POST /api/requests/{id}/attachments - Upload attachment

### Workflow Endpoints (Department A & Managers)
- GET /api/workflow/pending - Get pending requests
- POST /api/workflow/{id}/assign-path - Assign to workflow path
- POST /api/workflow/{id}/approve - Approve and move to next step
- POST /api/workflow/{id}/reject - Reject request
- POST /api/workflow/{id}/request-details - Request more details
- POST /api/workflow/{id}/complete - Mark as completed

### Admin Endpoints
- GET /api/admin/departments
- POST /api/admin/departments
- GET /api/admin/workflow-paths
- POST /api/admin/workflow-paths
- POST /api/admin/workflow-paths/{id}/steps
- GET /api/admin/users
- POST /api/admin/users/{id}/departments

## Frontend Structure (Vue.js)

```
frontend/
├── src/
│   ├── components/
│   │   ├── auth/
│   │   │   ├── LoginForm.vue
│   │   │   └── RegisterForm.vue
│   │   ├── requests/
│   │   │   ├── RequestForm.vue
│   │   │   ├── RequestList.vue
│   │   │   └── RequestDetail.vue
│   │   ├── workflow/
│   │   │   ├── PendingRequests.vue
│   │   │   ├── WorkflowActions.vue
│   │   │   └── PathSelector.vue
│   │   └── admin/
│   │       ├── DepartmentManager.vue
│   │       ├── WorkflowPathBuilder.vue
│   │       └── UserManagement.vue
│   ├── views/
│   │   ├── Dashboard.vue
│   │   ├── MyRequests.vue
│   │   ├── WorkflowDashboard.vue
│   │   └── AdminPanel.vue
│   ├── router/
│   │   └── index.js
│   ├── store/
│   │   └── index.js
│   └── App.vue
```

## Installation Commands

```bash
# Backend setup (already done)
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate

# Install frontend dependencies
npm install
npm install vue@3 vue-router@4 pinia axios

# Run development servers
php artisan serve  # Backend on http://localhost:8000
npm run dev        # Frontend on http://localhost:5173
```

This is a comprehensive foundation. Would you like me to continue with implementing specific controllers and Vue components?
