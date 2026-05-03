<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
