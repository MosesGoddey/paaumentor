<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('hackathon_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hackathon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained('hackathon_teams')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('github_url')->nullable();
            $table->string('demo_url')->nullable();
            $table->string('deck_url')->nullable();
            $table->enum('status', ['draft','submitted'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('hackathon_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('hackathon_submissions')->cascadeOnDelete();
            $table->foreignId('judge_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('innovation')->default(0);
            $table->unsignedTinyInteger('execution')->default(0);
            $table->unsignedTinyInteger('impact')->default(0);
            $table->unsignedTinyInteger('presentation')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['submission_id', 'judge_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('hackathon_scores');
        Schema::dropIfExists('hackathon_submissions');
    }
};
