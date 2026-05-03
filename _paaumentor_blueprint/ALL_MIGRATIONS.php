<?php
// ============================================================
//  PAAUMENTOR — All Database Migrations
//  Run: php artisan migrate
//  Or individually copy each class into database/migrations/
// ============================================================

// ---- FILE: 2024_01_01_000001_create_users_table.php ----
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('student_id')->unique()->nullable(); // e.g. 23CS1004
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['mentee', 'mentor', 'alumni', 'admin'])->default('mentee');
            $table->string('department')->nullable();
            $table->string('level')->nullable();          // 100L, 200L ... Alumni
            $table->text('bio')->nullable();
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();         // path to uploaded photo
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('availability')->nullable();   // e.g. 'Weekdays 4-6pm'
            $table->rememberToken();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('users'); }
};

// ---- FILE: 2024_01_01_000002_create_skills_table.php ----
return new class extends Migration {
    public function up(): void {
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();             // e.g. "Laravel", "Python"
            $table->string('category')->nullable();       // e.g. "Web", "Algorithms"
            $table->timestamps();
        });

        // Pivot: users <-> skills
        Schema::create('skill_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['has', 'wants'])->default('has');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('skill_user');
        Schema::dropIfExists('skills');
    }
};

// ---- FILE: 2024_01_01_000003_create_mentorships_table.php ----
return new class extends Migration {
    public function up(): void {
        Schema::create('mentorships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mentee_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'active', 'completed', 'rejected', 'cancelled'])
                  ->default('pending');
            $table->text('goal')->nullable();
            $table->string('topic')->nullable();
            $table->enum('session_type', ['video', 'voice', 'chat'])->default('video');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('mentorships'); }
};

// ---- FILE: 2024_01_01_000004_create_sessions_table.php ----
return new class extends Migration {
    public function up(): void {
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentorship_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['video', 'voice', 'chat'])->default('video');
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])
                  ->default('scheduled');
            $table->timestamp('scheduled_at');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('sessions'); }
};

// ---- FILE: 2024_01_01_000005_create_learning_paths_table.php ----
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

// ---- FILE: 2024_01_01_000006_create_messages_table.php ----
return new class extends Migration {
    public function up(): void {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentorship_id')->constrained()->cascadeOnDelete();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('body')->nullable();
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->enum('type', ['text', 'file', 'system'])->default('text');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
    }
};

// ---- FILE: 2024_01_01_000007_create_resources_table.php ----
return new class extends Migration {
    public function up(): void {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mentorship_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('file_path')->nullable();
            $table->string('url')->nullable();
            $table->enum('type', ['pdf', 'doc', 'link', 'image', 'other'])->default('other');
            $table->bigInteger('file_size')->nullable();  // bytes
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('resources'); }
};

// ---- FILE: 2024_01_01_000008_create_ratings_table.php ----
return new class extends Migration {
    public function up(): void {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentorship_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rater_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('ratee_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('score');         // 1-5
            $table->text('review')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('ratings'); }
};

// ---- FILE: 2024_01_01_000009_create_certificates_table.php ----
return new class extends Migration {
    public function up(): void {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learning_path_id')->constrained()->cascadeOnDelete();
            $table->string('certificate_id')->unique(); // e.g. PAAU-2025-00042
            $table->string('file_path')->nullable();
            $table->string('qr_code')->nullable();
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('certificates'); }
};

// ---- FILE: 2024_01_01_000010_create_notifications_table.php ----
return new class extends Migration {
    public function up(): void {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');                       // e.g. 'mentorship_request'
            $table->string('title');
            $table->text('body')->nullable();
            $table->json('data')->nullable();             // extra payload
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('notifications'); }
};
