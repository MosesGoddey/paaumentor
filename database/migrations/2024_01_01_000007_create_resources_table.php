<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mentorship_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('file_path')->nullable();
            $table->string('url')->nullable();
            $table->enum('type', ['pdf', 'doc', 'link', 'image', 'other'])->default('other');
            $table->bigInteger('file_size')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('resources'); }
};
