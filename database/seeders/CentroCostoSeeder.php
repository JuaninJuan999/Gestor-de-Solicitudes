<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CentroCosto;

class CentroCostoSeeder extends Seeder
{
    public function run()
    {
        // -- DPTO ADMON Y FINANCIERO --
        CentroCosto::create([
            'departamento' => 'DPTO ADMON Y FINANCIERO',
            'cc' => 212,
            'sc' => 1,
            'nombre_area' => 'GERENCIA ADMON Y FINANCIERA',
            'cuenta_contable' => '51'
        ]);
        CentroCosto::create([
            'departamento' => 'DPTO ADMON Y FINANCIERO',
            'cc' => 212,
            'sc' => 2,
            'nombre_area' => 'CONTABILIDAD',
            'cuenta_contable' => '51'
        ]);
        CentroCosto::create([
            'departamento' => 'DPTO ADMON Y FINANCIERO',
            'cc' => 212,
            'sc' => 3,
            'nombre_area' => 'COMPRAS',
            'cuenta_contable' => '51'
        ]);
        CentroCosto::create([
            'departamento' => 'DPTO ADMON Y FINANCIERO',
            'cc' => 212,
            'sc' => 4,
            'nombre_area' => 'TESORERIA',
            'cuenta_contable' => '51'
        ]);
        CentroCosto::create([
            'departamento' => 'DPTO ADMON Y FINANCIERO',
            'cc' => 212,
            'sc' => 5,
            'nombre_area' => 'PLANEACION Y PROYECTOS',
            'cuenta_contable' => '51'
        ]);
        CentroCosto::create([
            'departamento' => 'DPTO ADMON Y FINANCIERO',
            'cc' => 212,
            'sc' => 6,
            'nombre_area' => 'TICS',
            'cuenta_contable' => '51'
        ]);

        // -- CORPORATIVOS --
        CentroCosto::create([
            'departamento' => 'CORPORATIVOS',
            'cc' => 213,
            'sc' => 1,
            'nombre_area' => 'GERENCIA GENERAL',
            'cuenta_contable' => '2'
        ]);
        CentroCosto::create([
            'departamento' => 'CORPORATIVOS',
            'cc' => 213,
            'sc' => 2,
            'nombre_area' => 'JUNTA DIRECTIVA',
            'cuenta_contable' => '2'
        ]);
        CentroCosto::create([
            'departamento' => 'CORPORATIVOS',
            'cc' => 213,
            'sc' => 3,
            'nombre_area' => 'REVISORIA FISCAL',
            'cuenta_contable' => '2'
        ]);

        // -- DPTO COMERCIAL --
        CentroCosto::create([
            'departamento' => 'DPTO COMERCIAL',
            'cc' => 214,
            'sc' => 1,
            'nombre_area' => 'GERENCIA COMERCIAL',
            'cuenta_contable' => '52'
        ]);
        CentroCosto::create([
            'departamento' => 'DPTO COMERCIAL',
            'cc' => 214,
            'sc' => 2,
            'nombre_area' => 'MERCADO',
            'cuenta_contable' => '52'
        ]);
        CentroCosto::create([
            'departamento' => 'DPTO COMERCIAL',
            'cc' => 214,
            'sc' => 3,
            'nombre_area' => 'TRANSPORTES',
            'cuenta_contable' => '52'
        ]);

        // -- DPTO JURIDICO Y GESTION HUMANA --
        CentroCosto::create([
            'departamento' => 'DPTO JURIDICO Y GESTION HUMANA',
            'cc' => 215,
            'sc' => 1,
            'nombre_area' => 'GERENCIA JURIDICA Y GESTION HUMANA',
            'cuenta_contable' => '51'
        ]);
        CentroCosto::create([
            'departamento' => 'DPTO JURIDICO Y GESTION HUMANA',
            'cc' => 215,
            'sc' => 2,
            'nombre_area' => 'JURIDICA',
            'cuenta_contable' => '51'
        ]);
        CentroCosto::create([
            'departamento' => 'DPTO JURIDICO Y GESTION HUMANA',
            'cc' => 215,
            'sc' => 3,
            'nombre_area' => 'GESTION HUMANA',
            'cuenta_contable' => '51'
        ]);
        CentroCosto::create([
            'departamento' => 'DPTO JURIDICO Y GESTION HUMANA',
            'cc' => 215,
            'sc' => 4,
            'nombre_area' => 'ACCIONISTAS',
            'cuenta_contable' => '51'
        ]);
        CentroCosto::create([
            'departamento' => 'DPTO JURIDICO Y GESTION HUMANA',
            'cc' => 215,
            'sc' => 6,
            'nombre_area' => 'SST - SISO',
            'cuenta_contable' => '51'
        ]);

        // -- GANADERO --
        CentroCosto::create([
            'departamento' => 'GANADERO',
            'cc' => 219,
            'sc' => 1,
            'nombre_area' => 'FOMENTO GANADERO',
            'cuenta_contable' => '52'
        ]);
                // -- EXPORTACIÓN --
        CentroCosto::create([
            'departamento' => 'EXPORTACIÓN',
            'cc' => 222,
            'sc' => 1,
            'nombre_area' => 'EXPORTACIONES',
            'cuenta_contable' => '52'
        ]);

        // -- DPTO CALIDAD --
        CentroCosto::create([
            'departamento' => 'DPTO CALIDAD',
            'cc' => 306,
            'sc' => 1,
            'nombre_area' => 'GERENCIA CALIDAD',
            'cuenta_contable' => '52'
        ]);
        CentroCosto::create([
            'departamento' => 'DPTO CALIDAD',
            'cc' => 306,
            'sc' => 2,
            'nombre_area' => 'LIMPIEZA Y DESINFECCION',
            'cuenta_contable' => '52'
        ]);
        CentroCosto::create([
            'departamento' => 'DPTO CALIDAD',
            'cc' => 306,
            'sc' => 3,
            'nombre_area' => 'LAVANDERIA',
            'cuenta_contable' => '52'
        ]);
        CentroCosto::create([
            'departamento' => 'DPTO CALIDAD',
            'cc' => 306,
            'sc' => 7,
            'nombre_area' => 'LABORATORIO',
            'cuenta_contable' => '52'
        ]);
        CentroCosto::create([
            'departamento' => 'DPTO CALIDAD',
            'cc' => 306,
            'sc' => 9,
            'nombre_area' => 'INVIMA',
            'cuenta_contable' => '52'
        ]);
        CentroCosto::create([
            'departamento' => 'DPTO CALIDAD',
            'cc' => 306,
            'sc' => 10,
            'nombre_area' => 'INNOVACIÓN (IDI)',
            'cuenta_contable' => '52'
        ]);

        // -- DPTO DE PRODUCCION --
        CentroCosto::create([
            'departamento' => 'DPTO DE PRODUCCION',
            'cc' => 307,
            'sc' => 1,
            'nombre_area' => 'PRODUCCION BENEFICIO',
            'cuenta_contable' => '73'
        ]);
        CentroCosto::create([
            'departamento' => 'DPTO DE PRODUCCION',
            'cc' => 307,
            'sc' => 2,
            'nombre_area' => 'RECEPCION Y PESAJE',
            'cuenta_contable' => '73'
        ]);
        CentroCosto::create([
            'departamento' => 'DPTO DE PRODUCCION',
            'cc' => 307,
            'sc' => 3,
            'nombre_area' => 'LINEA DE SACRIFICIO',
            'cuenta_contable' => '73'
        ]);
        CentroCosto::create([
            'departamento' => 'DPTO DE PRODUCCION',
            'cc' => 307,
            'sc' => 4,
            'nombre_area' => 'SUBPRODUCTOS COMESTIBLES',
            'cuenta_contable' => '73'
        ]);
        CentroCosto::create([
            'departamento' => 'DPTO DE PRODUCCION',
            'cc' => 307,
            'sc' => 6,
            'nombre_area' => 'LOGISTICA',
            'cuenta_contable' => '73'
        ]);
        CentroCosto::create([
            'departamento' => 'DPTO DE PRODUCCION',
            'cc' => 307,
            'sc' => 9,
            'nombre_area' => 'MANTENIMIENTO',
            'cuenta_contable' => '73'
        ]);
        CentroCosto::create([
            'departamento' => 'DPTO DE PRODUCCION',
            'cc' => 307,
            'sc' => 10,
            'nombre_area' => 'PTAR',
            'cuenta_contable' => '73'
        ]);
        CentroCosto::create([
            'departamento' => 'DPTO DE PRODUCCION',
            'cc' => 307,
            'sc' => 11,
            'nombre_area' => 'PTAP',
            'cuenta_contable' => '73'
        ]);
        CentroCosto::create([
            'departamento' => 'DPTO DE PRODUCCION',
            'cc' => 307,
            'sc' => 12,
            'nombre_area' => 'AMBIENTAL',
            'cuenta_contable' => '73'
        ]);
        CentroCosto::create([
            'departamento' => 'DPTO DE PRODUCCION',
            'cc' => 307,
            'sc' => 13,
            'nombre_area' => 'ABONO',
            'cuenta_contable' => '73'
        ]);

        // -- DESPOSTE --
        CentroCosto::create([
            'departamento' => 'DESPOSTE',
            'cc' => 309,
            'sc' => 1,
            'nombre_area' => 'PRODUCCION DESPOSTE',
            'cuenta_contable' => '73'
        ]);
        CentroCosto::create([
            'departamento' => 'DESPOSTE',
            'cc' => 309,
            'sc' => 2,
            'nombre_area' => 'LINEA DESPOSTE',
            'cuenta_contable' => '73'
        ]);
        CentroCosto::create([
            'departamento' => 'DESPOSTE',
            'cc' => 309,
            'sc' => 3,
            'nombre_area' => 'LOGISTICA DESPOSTE',
            'cuenta_contable' => '73'
        ]);
        CentroCosto::create([
            'departamento' => 'DESPOSTE',
            'cc' => 309,
            'sc' => 4,
            'nombre_area' => 'CALIDAD DESPOSTE',
            'cuenta_contable' => '73'
        ]);
        CentroCosto::create([
            'departamento' => 'DESPOSTE',
            'cc' => 309,
            'sc' => 5,
            'nombre_area' => 'LIMPIEZA Y DESINFECCIÓN DESPOSTE',
            'cuenta_contable' => '73'
        ]);
        CentroCosto::create([
            'departamento' => 'DESPOSTE',
            'cc' => 309,
            'sc' => 6,
            'nombre_area' => 'LAVANDERIA DESPOSTE',
            'cuenta_contable' => '73'
        ]);
        CentroCosto::create([
            'departamento' => 'DESPOSTE',
            'cc' => 309,
            'sc' => 7,
            'nombre_area' => 'PTAR DESPOSTE',
            'cuenta_contable' => '73'
        ]);
        CentroCosto::create([
            'departamento' => 'DESPOSTE',
            'cc' => 309,
            'sc' => 8,
            'nombre_area' => 'PTAP DESPOSTE',
            'cuenta_contable' => '73'
        ]);
        CentroCosto::create([
            'departamento' => 'DESPOSTE',
            'cc' => 309,
            'sc' => 9,
            'nombre_area' => 'MANTENIMIENTO DESPOSTE',
            'cuenta_contable' => '73'
        ]);
        CentroCosto::create([
            'departamento' => 'DESPOSTE',
            'cc' => 309,
            'sc' => 10,
            'nombre_area' => 'PORCIONADO',
            'cuenta_contable' => '73'
        ]);

    }
}
