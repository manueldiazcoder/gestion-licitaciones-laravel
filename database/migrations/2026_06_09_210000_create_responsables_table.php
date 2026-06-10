<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('responsables', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_completo', 255);
            $table->string('numero_telefono', 30);
            $table->string('correo_electronico', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('responsables');
    }
};
