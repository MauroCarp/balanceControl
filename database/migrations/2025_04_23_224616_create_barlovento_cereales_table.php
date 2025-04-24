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
        Schema::create('barlovento_cereales', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->date('establecimiento');
            $table->string('cereal');
            $table->string('cartaPorte');
            $table->string('vendedor');
            $table->float('pesoBruto');
            $table->float('tara');
            $table->integer('humedad');
            $table->integer('mermaHumedad');
            $table->string('calidad');
            $table->string('materiasExtraneas');
            $table->boolean('tierra');
            $table->string('destino');
            $table->string('observaciones');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barlovento_cereales');
        
    }
};
