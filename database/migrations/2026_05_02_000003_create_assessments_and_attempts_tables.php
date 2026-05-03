<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_path_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('passing_score')->default(70);       // percent
            $table->unsignedSmallInteger('time_per_question')->default(90);  // seconds
            $table->unsignedTinyInteger('questions_per_attempt')->default(15);
            $table->boolean('questions_ready')->default(false);
            $table->timestamp('questions_generated_at')->nullable();
            $table->timestamps();

            $table->unique('learning_path_id');
        });

        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->text('question');
            $table->json('options');        // ["opt A", "opt B", "opt C", "opt D"]
            $table->unsignedTinyInteger('correct_answer'); // 0-3 index
            $table->unsignedTinyInteger('points')->default(1);
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('assessment_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('certificate_request_id')->constrained()->cascadeOnDelete();
            $table->json('question_ids');       // the 15 IDs randomly picked for this attempt
            $table->json('answers')->nullable(); // {question_id: chosen_index}
            $table->unsignedTinyInteger('score')->nullable();     // percentage
            $table->unsignedTinyInteger('max_score')->default(15);
            $table->boolean('passed')->nullable();
            $table->unsignedTinyInteger('tab_switches')->default(0);
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_attempts');
        Schema::dropIfExists('assessment_questions');
        Schema::dropIfExists('assessments');
    }
};
