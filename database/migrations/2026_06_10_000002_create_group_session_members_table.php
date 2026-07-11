<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_session_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['host', 'member'])->default('member');
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            $table->unique(['group_session_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_session_members');
    }
};
