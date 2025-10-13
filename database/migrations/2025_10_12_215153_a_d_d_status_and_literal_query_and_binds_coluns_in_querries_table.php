<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('querries', 'status')) {
            Schema::table('querries', function (Blueprint $table) {
                $table->string('status')->nullable(false)->default('pending');
                $table->text('literal_query')->nullable(true);
                $table->jsonb('binds')->nullable(true)->default(json_encode([]));
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('querries', 'status')) {
            Schema::table('querries', function (Blueprint $table) {
                $table->dropColumn(['status', 'literal_query', 'binds']);
            });
        }
    }
};
