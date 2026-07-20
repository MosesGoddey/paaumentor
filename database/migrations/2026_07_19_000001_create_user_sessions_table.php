<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dedicated table for Laravel HTTP sessions.
 *
 * We can't use the default 'sessions' table name because this app already
 * uses 'sessions' for mentor/call sessions (MentorSession). Storing HTTP
 * sessions in the database (instead of files) avoids file-lock race
 * conditions on Windows when concurrent requests — e.g. call polling plus
 * a form submit — touch the same session at once, which was causing
 * intermittent 419 "Page Expired" errors on logout/login.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_sessions');
    }
};
