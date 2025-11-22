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
        Schema::create('request_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('requests')->onDelete('cascade');
            $table->foreignId('evaluation_question_id')->constrained('evaluation_questions')->onDelete('cascade');
            $table->foreignId('evaluated_by')->constrained('users')->onDelete('cascade');
            $table->integer('score'); // Score out of 100 for this question
            $table->text('notes')->nullable(); // Optional notes for this answer
            $table->timestamps();

            // Unique constraint: one evaluation per question per request
            $table->unique(['request_id', 'evaluation_question_id'], 'request_question_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_evaluations');
    }
};
