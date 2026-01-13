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
        Schema::table('solicitudes', function (Blueprint $table) {
            // nullable() porque dijiste que NO TODAS requieren aprobación
            $table->foreignId('supervisor_id')
                  ->nullable()
                  ->after('user_id') // Para que quede ordenado visualmente en la DB
                  ->constrained('users')
                  ->onDelete('set null'); // Si borran el usuario supervisor, no se borra la solicitud
        });
    }

    public function down(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->dropForeign(['supervisor_id']);
            $table->dropColumn('supervisor_id');
        });
    }

};
