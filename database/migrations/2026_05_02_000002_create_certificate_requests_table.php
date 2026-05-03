<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificate_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_path_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mentee_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mentor_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending_assessment', 'pending_verifier', 'approved', 'rejected'])
                  ->default('pending_assessment');
            $table->unsignedTinyInteger('assessment_score')->nullable();
            $table->timestamp('assessment_passed_at')->nullable();
            $table->foreignId('verifier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('verifier_note')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->unique('learning_path_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificate_requests');
    }
};
