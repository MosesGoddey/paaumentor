<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('shared_resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedBigInteger('file_size')->default(0);
            $table->string('mime_type')->nullable();
            $table->foreignId('uploader_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('study_group_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('mentorship_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_public')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('shared_resources'); }
};
