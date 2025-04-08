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
        Schema::create('barlovento_ingresos', function (Blueprint $table) {
            $table->id();
            $table->string('consignatario');
            $table->string('comisionista');
            $table->string('dte');
            $table->string('origen_cantidad');
            $table->string('origen_categoria');
            $table->string('origen_distancia');
            $table->string('origen_pesoBruto');
            $table->string('origen_pesoNeto');
            $table->string('origen_desbaste');
            $table->string('destino_cantidad');
            $table->string('destino_categoria');
            $table->string('destino_pesoBruto');
            $table->string('destino_tara');
            $table->string('precioKg');
            $table->string('precioFlete');
            $table->string('precioOtrosGastos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barlovento_ingresos');
    }
};
