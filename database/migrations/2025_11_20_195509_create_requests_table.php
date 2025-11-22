<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User who submitted
            $table->foreignId('current_department_id')->nullable()->constrained('departments');
            $table->foreignId('current_user_id')->nullable()->constrained('users'); // Current assignee
            $table->foreignId('workflow_path_id')->nullable()->constrained();
            $table->enum('status', ['draft', 'pending', 'in_review', 'need_more_details', 'approved', 'rejected', 'completed'])->default('draft');
            $table->text('rejection_reason')->nullable();
            $table->text('additional_details')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
