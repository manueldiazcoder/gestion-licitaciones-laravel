<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop foreign key en ofertas que apunta a procesos
        Schema::table('ofertas', function (Blueprint $table) {
            $table->dropForeign(['proceso']);
        });

        // 2. Renombrar la tabla
        Schema::rename('procesos', 'licitaciones');

        // 3. Renombrar la columna PK
        Schema::table('licitaciones', function (Blueprint $table) {
            $table->renameColumn('codigo_proceso', 'codigo_licitacion');
        });

        // 4. Re-crear foreign key apuntando a licitaciones
        Schema::table('ofertas', function (Blueprint $table) {
            $table->foreign('proceso')->references('codigo_licitacion')->on('licitaciones');
        });
    }

    public function down(): void
    {
        Schema::table('ofertas', function (Blueprint $table) {
            $table->dropForeign(['proceso']);
        });

        Schema::table('licitaciones', function (Blueprint $table) {
            $table->renameColumn('codigo_licitacion', 'codigo_proceso');
        });

        Schema::rename('licitaciones', 'procesos');

        Schema::table('ofertas', function (Blueprint $table) {
            $table->foreign('proceso')->references('codigo_proceso')->on('procesos');
        });
    }
};
