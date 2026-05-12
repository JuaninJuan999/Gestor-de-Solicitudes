<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * La columna unidad guarda abreviaturas de unidad (UND, KG, etc.), no números.
     * En PostgreSQL como integer falla al insertar texto (SQLSTATE 22P02).
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE items ALTER COLUMN unidad TYPE VARCHAR(100) USING CASE WHEN unidad IS NULL THEN NULL ELSE unidad::text END');
        } elseif (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE items MODIFY unidad VARCHAR(100) NULL');
        }
        // SQLite: si hace falta, migrate:fresh o alter manual; la mayoría de prod usa MySQL/PG.
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE items ALTER COLUMN unidad TYPE INTEGER USING CASE WHEN unidad ~ \'^[0-9]+$\' THEN unidad::integer ELSE NULL END');
        } elseif (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE items MODIFY unidad INT NULL');
        }
    }
};
