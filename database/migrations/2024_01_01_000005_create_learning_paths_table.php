<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('learning_paths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mentee_id')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'completed', 'archived'])->default('active');
            $table->date('due_date')->nullable();
            $table->timestamps();
        });

        Schema::create('learning_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_path_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('learning_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_module_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->integer('max_score')->default(100);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });

        Schema::create('task_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->string('file_path')->nullable();
            $table->enum('status', ['submitted', 'graded', 'rejected'])->default('submitted');
            $table->integer('score')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('task_submissions');
        Schema::dropIfExists('learning_tasks');
        Schema::dropIfExists('learning_modules');
        Schema::dropIfExists('learning_paths');
    }
};
