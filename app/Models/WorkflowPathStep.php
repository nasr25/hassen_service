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
