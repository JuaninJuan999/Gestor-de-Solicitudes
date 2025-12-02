<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArchivoToComentariosTable extends Migration
{
    public function up()
    {
        Schema::table('comentarios', function (Blueprint $table) {
            $table->string('archivo')->nullable()->after('comentario');
        });
    }

    public function down()
    {
        Schema::table('comentarios', function (Blueprint $table) {
            $table->dropColumn('archivo');
        });
    }
}
