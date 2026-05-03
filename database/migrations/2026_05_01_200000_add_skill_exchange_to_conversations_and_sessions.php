<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->unsignedBigInteger('mentorship_id')->nullable()->change();
            $table->foreignId('skill_exchange_request_id')
                  ->nullable()
                  ->after('mentorship_id')
                  ->constrained('skill_exchange_requests')
                  ->cascadeOnDelete();
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('mentorship_id')->nullable()->change();
            $table->foreignId('skill_exchange_request_id')
                  ->nullable()
                  ->after('mentorship_id')
                  ->constrained('skill_exchange_requests')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropForeign(['skill_exchange_request_id']);
            $table->dropColumn('skill_exchange_request_id');
            $table->unsignedBigInteger('mentorship_id')->nullable(false)->change();
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->dropForeign(['skill_exchange_request_id']);
            $table->dropColumn('skill_exchange_request_id');
            $table->unsignedBigInteger('mentorship_id')->nullable(false)->change();
        });
    }
};
