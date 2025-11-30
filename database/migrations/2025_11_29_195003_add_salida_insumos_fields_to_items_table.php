<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('area_consumo')->nullable()->after('bodega');
            $table->string('centro_costos_item')->nullable()->after('area_consumo');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['area_consumo', 'centro_costos_item']);
        });
    }
};
