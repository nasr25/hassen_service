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
