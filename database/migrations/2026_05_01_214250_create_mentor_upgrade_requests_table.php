<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mentor_upgrade_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mentee_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('mentor_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->enum('status', ['pending', 'recommended', 'approved', 'rejected'])
                  ->default('pending');

            // Mentor recommendation
            $table->text('mentor_note')->nullable();
            $table->timestamp('mentor_recommended_at')->nullable();

            // Admin decision
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_note')->nullable();
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentor_upgrade_requests');
    }
};
