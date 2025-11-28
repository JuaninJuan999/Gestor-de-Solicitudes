<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCentrosCostosTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('centros_costos', function (Blueprint $table) {
            $table->id();
            $table->string('departamento');
            $table->integer('cc');
            $table->integer('sc');
            $table->string('nombre_area');
            $table->string('cuenta_contable')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('centros_costos');
    }
}

