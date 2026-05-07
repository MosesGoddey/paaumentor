<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('hackathon_teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hackathon_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('join_code', 8)->unique();
            $table->string('track')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->foreignId('coach_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('coach_status', ['pending','accepted'])->nullable();
            $table->timestamps();
        });

        Schema::create('hackathon_team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('hackathon_teams')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_lead')->default(false);
            $table->timestamps();
            $table->unique(['team_id', 'user_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('hackathon_team_members');
        Schema::dropIfExists('hackathon_teams');
    }
};
