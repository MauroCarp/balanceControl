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
            $table->integer('origen_distancia');
            $table->float('origen_pesoBruto');
            $table->float('origen_pesoNeto');
            $table->integer('origen_desbaste');
            $table->integer('destino_cantidad');
            $table->string('destino_categoria');
            $table->float('destino_pesoBruto');
            $table->float('destino_tara');
            $table->float('precioKg');
            $table->float('precioFlete');
            $table->float('precioOtrosGastos');
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
