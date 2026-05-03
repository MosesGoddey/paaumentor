<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema, DB};

return new class extends Migration
{
    public function up(): void
    {
        // Extend the status enum to include pending_mentor_reflection
        DB::statement("ALTER TABLE certificate_requests MODIFY COLUMN status
            ENUM('pending_assessment','pending_mentor_reflection','pending_verifier','approved','rejected')
            NOT NULL DEFAULT 'pending_assessment'");

        Schema::table('certificate_requests', function (Blueprint $table) {
            $table->text('mentor_reflection')->nullable()->after('assessment_passed_at');
            $table->timestamp('mentor_reflection_submitted_at')->nullable()->after('mentor_reflection');
        });
    }

    public function down(): void
    {
        Schema::table('certificate_requests', function (Blueprint $table) {
            $table->dropColumn(['mentor_reflection', 'mentor_reflection_submitted_at']);
        });

        DB::statement("ALTER TABLE certificate_requests MODIFY COLUMN status
            ENUM('pending_assessment','pending_verifier','approved','rejected')
            NOT NULL DEFAULT 'pending_assessment'");
    }
};
