<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('silos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo', 100)->nullable()->unique();
            $table->unsignedBigInteger('capacidad_kg');
            $table->unsignedBigInteger('stock_actual_kg')->default(0);
            $table->string('cereal')->nullable();
            $table->decimal('humedad', 5, 2)->nullable();
            $table->enum('estado', ['activo', 'vacio', 'lleno', 'en_reparacion'])->default('vacio');
            $table->string('ubicacion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('silos');
    }
};
