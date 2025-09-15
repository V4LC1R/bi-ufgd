<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('querries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('connection_id');
            $table->string('hash')->nullable();
            $table->string('type')->nullable();
            $table->jsonb('struct')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('connection_id')->references('id')->on('connections');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('querries');
    }
};
