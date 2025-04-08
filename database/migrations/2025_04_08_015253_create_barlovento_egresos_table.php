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
        Schema::create('barlovento_egresos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->integer('dte');
            $table->string('tipoDestino');
            $table->string('lugarDestino');
            $table->string('frigorificoDestino');
            $table->float('tara');
            $table->float('pesoBruto');
            $table->string('categoria');
            $table->integer('cantidad');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barlovento_egresos');
    }
};
