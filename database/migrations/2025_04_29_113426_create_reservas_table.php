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
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id'); // Relación con el usuario (cliente)
            $table->unsignedBigInteger('sala_id'); // Relación con la sala
            $table->unsignedBigInteger('estado_id'); // Relación con el estado
            $table->dateTime('fecha');
            $table->time('hora_inicio'); // Hora de inicio de la reserva
            $table->time('hora_fin'); // Hora de fin de la reserva
            $table->timestamps();

            // Definir las claves foráneas
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sala_id')->references('id')->on('salas')->onDelete('cascade');
            $table->foreign('estado_id')->references('id')->on('estados')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
