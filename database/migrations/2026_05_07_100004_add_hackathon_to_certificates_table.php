<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('certificates', function (Blueprint $table) {
            // Make learning_path_id nullable so hackathon certs don't need it
            $table->dropForeign(['learning_path_id']);
            $table->unsignedBigInteger('learning_path_id')->nullable()->change();
            $table->foreign('learning_path_id')->references('id')->on('learning_paths')->nullOnDelete();

            $table->foreignId('hackathon_team_id')->nullable()->after('learning_path_id')
                  ->constrained('hackathon_teams')->nullOnDelete();
            $table->string('placement', 20)->nullable()->after('hackathon_team_id');
        });
    }

    public function down(): void {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropForeign(['hackathon_team_id']);
            $table->dropColumn(['hackathon_team_id', 'placement']);
            $table->dropForeign(['learning_path_id']);
            $table->unsignedBigInteger('learning_path_id')->nullable(false)->change();
            $table->foreign('learning_path_id')->references('id')->on('learning_paths')->cascadeOnDelete();
        });
    }
};
