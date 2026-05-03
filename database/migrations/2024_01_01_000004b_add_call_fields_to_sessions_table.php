<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('sessions', function (Blueprint $table) {
            $table->string('room')->nullable()->after('description');
            $table->enum('call_outcome', ['answered', 'missed'])->nullable()->after('status');
        });
    }
    public function down(): void {
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropColumn(['room', 'call_outcome']);
        });
    }
};
