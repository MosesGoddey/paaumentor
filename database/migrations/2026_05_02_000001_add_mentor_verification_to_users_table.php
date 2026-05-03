<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema, DB};

return new class extends Migration
{
    public function up(): void
    {
        // Add verifier to the role enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('mentee','mentor','alumni','admin','verifier') NOT NULL DEFAULT 'mentee'");

        Schema::table('users', function (Blueprint $table) {
            $table->enum('mentor_status', ['pending', 'active', 'suspended'])
                  ->nullable()
                  ->after('role')
                  ->comment('Only set for mentor/alumni roles');

            $table->string('github_url', 255)->nullable()->after('bio');
            $table->string('linkedin_url', 255)->nullable()->after('github_url');
        });

        // All existing mentors and alumni are already verified — mark them active
        DB::statement("UPDATE users SET mentor_status = 'active' WHERE role IN ('mentor', 'alumni')");
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['mentor_status', 'github_url', 'linkedin_url']);
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('mentee','mentor','alumni','admin') NOT NULL DEFAULT 'mentee'");
    }
};
