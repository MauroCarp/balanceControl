<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paihuen_cereales', function (Blueprint $table) {
            $table->unsignedBigInteger('silo_id')->nullable()->after('destino');
        });
    }

    public function down(): void
    {
        Schema::table('paihuen_cereales', function (Blueprint $table) {
            $table->dropColumn('silo_id');
        });
    }
};
