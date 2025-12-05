<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->string('funcion_formato')->nullable()->after('tipo_solicitud');
            $table->text('justificacion')->nullable()->after('descripcion');
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->dropColumn(['funcion_formato', 'justificacion']);
        });
    }
};
