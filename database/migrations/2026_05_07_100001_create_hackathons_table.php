<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('hackathons', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('theme')->nullable();
            $table->text('rules')->nullable();
            $table->json('tracks')->nullable();
            $table->json('judge_ids')->nullable();
            $table->enum('status', ['draft','open','ongoing','judging','completed'])->default('draft');
            $table->date('registration_deadline')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedTinyInteger('max_team_size')->default(4);
            $table->text('prizes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('hackathons'); }
};
