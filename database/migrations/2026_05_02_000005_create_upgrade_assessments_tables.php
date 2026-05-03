<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema, DB};

return new class extends Migration
{
    public function up(): void
    {
        // Add pending_assessment to upgrade_requests status enum
        DB::statement("ALTER TABLE mentor_upgrade_requests MODIFY COLUMN status ENUM('pending_assessment','pending','recommended','approved','rejected') NOT NULL DEFAULT 'pending_assessment'");

        Schema::create('upgrade_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('upgrade_request_id')->constrained('mentor_upgrade_requests')->cascadeOnDelete();
            $table->unsignedInteger('passing_score')->default(70);
            $table->unsignedInteger('time_per_question')->default(90);
            $table->unsignedInteger('questions_per_attempt')->default(15);
            $table->boolean('questions_ready')->default(false);
            $table->timestamp('questions_generated_at')->nullable();
            $table->timestamps();
        });

        Schema::create('upgrade_assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('upgrade_assessment_id')->constrained('upgrade_assessments')->cascadeOnDelete();
            $table->text('question');
            $table->json('options');
            $table->unsignedTinyInteger('correct_answer');
            $table->unsignedTinyInteger('points')->default(1);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('upgrade_assessment_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('upgrade_assessment_id')->constrained('upgrade_assessments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('upgrade_request_id')->constrained('mentor_upgrade_requests')->cascadeOnDelete();
            $table->json('question_ids');
            $table->json('answers')->nullable();
            $table->unsignedInteger('score')->nullable();
            $table->unsignedInteger('max_score')->default(15);
            $table->boolean('passed')->nullable();
            $table->unsignedInteger('tab_switches')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upgrade_assessment_attempts');
        Schema::dropIfExists('upgrade_assessment_questions');
        Schema::dropIfExists('upgrade_assessments');
        DB::statement("ALTER TABLE mentor_upgrade_requests MODIFY COLUMN status ENUM('pending','recommended','approved','rejected') NOT NULL DEFAULT 'pending'");
    }
};
