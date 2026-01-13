<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('users', function (Blueprint $table) {
        // Campo role: string para guardar 'admin', 'user', 'jefe', etc.
        // Lo ponemos nullable o con default 'user'
        $table->string('role')->default('user')->after('email');
        
        // Campo para estado activo/inactivo
        $table->boolean('is_active')->default(true)->after('role');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['role', 'is_active']);
    });
}

};
