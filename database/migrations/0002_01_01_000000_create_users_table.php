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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entity_id')->comment('Id da Faculdade');
            $table->string('name')->comment("Nome do usuario");
            $table->string('email')->unique()->comment("Email do usuario");
            $table->string(column: 'document')->unique()->comment("Documento do usuario");
            $table->boolean('email_is_verify')->default(false)->comment("Validacao de Email");
            $table->string('password');
            $table->timestamps();

            $table->foreign('entity_id')->references('id')->on('entities');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
