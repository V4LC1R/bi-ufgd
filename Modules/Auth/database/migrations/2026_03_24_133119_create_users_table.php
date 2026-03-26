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
        DB::statement('CREATE SCHEMA IF NOT EXISTS auth');

        Schema::create('auth.users', function (Blueprint $table) {
            $table->id();

            $table->string('name')
                ->nullable(false)
                ->comment('Name user in the aplication');

            $table->string('password')
                ->nullable(false)
                ->comment('Password to acess aplication');

            $table->string('email')
                ->nullable(true)
                ->unique()
                ->comment('Email to acess aplication');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auth.users');
    }
};
