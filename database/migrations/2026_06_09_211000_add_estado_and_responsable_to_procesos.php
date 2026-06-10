<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('procesos', function (Blueprint $table) {
            $table->string('estado', 50)->default('borrador')->after('moneda');
            $table->foreignId('responsable_id')->nullable()->constrained('responsables')->nullOnDelete()->after('estado');
            $table->foreignId('creador_id')->nullable()->constrained('users')->nullOnDelete()->after('responsable_id');
        });
    }

    public function down(): void
    {
        Schema::table('procesos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('creador_id');
            $table->dropConstrainedForeignId('responsable_id');
            $table->dropColumn('estado');
        });
    }
};
