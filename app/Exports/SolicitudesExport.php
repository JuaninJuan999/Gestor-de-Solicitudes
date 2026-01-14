<?php

namespace App\Exports;

use App\Models\Solicitud;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing; // Para el logo
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SolicitudesExport
{
    protected $fechaInicio;
    protected $fechaFin;
    protected $estado;
    protected $tipoSolicitud;

    public function __construct($fechaInicio = null, $fechaFin = null, $estado = null, $tipoSolicitud = null)
    {
        $this->fechaInicio    = $fechaInicio;
        $this->fechaFin       = $fechaFin;
        $this->estado         = $estado;
        $this->tipoSolicitud  = $tipoSolicitud;
    }

    public function download(string $fileName = 'reporte-solicitudes.xlsx'): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Solicitudes');

        // =======================
        // 1. LOGO INSTITUCIONAL
        // =======================
        $logoPath = public_path('images/logos/logo2.png'); // Asegúrate que exista esta imagen
        if (file_exists($logoPath)) {
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setDescription('Logo Institucional');
            $drawing->setPath($logoPath);
            $drawing->setHeight(50); // Altura en píxeles
            $drawing->setCoordinates('A1');
            $drawing->setWorksheet($sheet);
        }

        // =======================
        // 2. TÍTULO DEL REPORTE
        // =======================
        // Fusionar celdas A1 hasta H3 para el encabezado
        $sheet->mergeCells('A1:H3'); 
        $sheet->setCellValue('A1', "REPORTE DE GESTIÓN DE SOLICITUDES\nGenerado el: " . date('d/m/Y H:i'));
        
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1E40AF'));
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1')->getAlignment()->setWrapText(true);

        // Ajustar altura de las filas del encabezado
        $sheet->getRowDimension(1)->setRowHeight(20);
        $sheet->getRowDimension(2)->setRowHeight(20);
        $sheet->getRowDimension(3)->setRowHeight(20);

        // =======================
        // 3. TABLA DE DATOS
        // =======================
        
        // Fila donde empiezan los encabezados de la tabla (ahora bajamos a la fila 5)
        $startRow = 5; 

        // Encabezados
        $headings = [
            'Consecutivo', 'Tipo', 'Estado', 'Usuario', 'Área', 'Centro Costos', 'Fecha Creación', 'Items'
        ];

        $colLetter = 'A';
        foreach ($headings as $heading) {
            $sheet->setCellValue($colLetter . $startRow, $heading);
            $colLetter++;
        }

        $this->styleHeader($sheet, count($headings), $startRow);

        // Consulta de datos
        $query = Solicitud::with('user');

        if ($this->fechaInicio) $query->whereDate('created_at', '>=', $this->fechaInicio);
        if ($this->fechaFin) $query->whereDate('created_at', '<=', $this->fechaFin);
        if ($this->estado) $query->where('estado', $this->estado);
        if ($this->tipoSolicitud) $query->where('tipo_solicitud', $this->tipoSolicitud);

        $solicitudes = $query->orderBy('created_at', 'desc')->get();

        // Llenar datos
        $row = $startRow + 1;
        foreach ($solicitudes as $solicitud) {
            $sheet->setCellValue('A' . $row, $solicitud->consecutivo ?? 'N/A');
            $sheet->setCellValue('B' . $row, ucwords(str_replace('_', ' ', $solicitud->tipo_solicitud)));
            $sheet->setCellValue('C' . $row, ucfirst(str_replace('_', ' ', $solicitud->estado)));
            $sheet->setCellValue('D' . $row, $solicitud->user->name ?? 'N/A');
            $sheet->setCellValue('E' . $row, $solicitud->user->area ?? 'N/A');
            $sheet->setCellValue('F' . $row, $solicitud->centro_costos ?? '');
            $sheet->setCellValue('G' . $row, $solicitud->created_at->format('d/m/Y H:i'));
            $sheet->setCellValue('H' . $row, $solicitud->items->count());
            $row++;
        }

        $this->styleData($sheet, $row - 1, count($headings), $startRow + 1);

        // =======================
        // GRÁFICO 1: ESTADOS
        // =======================
        $statsEstados = [
            'Pendiente'  => $solicitudes->where('estado', 'pendiente')->count(),
            'Aprobado Sup.' => $solicitudes->where('estado', 'aprobado_supervisor')->count(),
            'En Proceso' => $solicitudes->where('estado', 'en_proceso')->count(),
            'Finalizada' => $solicitudes->where('estado', 'finalizada')->count(),
            'Rechazada'  => $solicitudes->where('estado', 'rechazada')->count(),
        ];

        // Datos auxiliares (Columna J y K)
        $sheet->setCellValue('J' . $startRow, 'Estado');
        $sheet->setCellValue('K' . $startRow, 'Cant.');
        
        $grafRow = $startRow + 1;
        foreach ($statsEstados as $estado => $cant) {
            $sheet->setCellValue('J' . $grafRow, $estado);
            $sheet->setCellValue('K' . $grafRow, $cant);
            $grafRow++;
        }

        // Definir rangos dinámicos
        $lastStatRow = $grafRow - 1;
        
        $chart1 = $this->createChart(
            'Solicitudes por Estado', 
            "Solicitudes!\$J$" . ($startRow+1) . ":\$J$" . $lastStatRow, 
            "Solicitudes!\$K$" . ($startRow+1) . ":\$K$" . $lastStatRow,
            count($statsEstados)
        );
        $chart1->setTopLeftPosition('J' . ($startRow + 10));
        $chart1->setBottomRightPosition('R' . ($startRow + 25));
        $sheet->addChart($chart1);

        // =======================
        // GRÁFICO 2: TIPOS (CORREGIDO: Incluye Mtto)
        // =======================
        $statsTipos = [
            'Estándar'         => $solicitudes->where('tipo_solicitud', 'estandar')->count(),
            'Traslado Bodegas' => $solicitudes->where('tipo_solicitud', 'traslado_bodegas')->count(),
            'Pedidos'          => $solicitudes->where('tipo_solicitud', 'solicitud_pedidos')->count(),
            'Mantenimiento'    => $solicitudes->where('tipo_solicitud', 'solicitud_mtto')->count(), // ¡Agregado!
        ];

        // Datos auxiliares (Columna M y N)
        $sheet->setCellValue('M' . $startRow, 'Tipo');
        $sheet->setCellValue('N' . $startRow, 'Cant.');

        $grafRow2 = $startRow + 1;
        foreach ($statsTipos as $tipo => $cant) {
            $sheet->setCellValue('M' . $grafRow2, $tipo);
            $sheet->setCellValue('N' . $grafRow2, $cant);
            $grafRow2++;
        }
        
        $lastStatRow2 = $grafRow2 - 1;

        $chart2 = $this->createChart(
            'Solicitudes por Tipo', 
            "Solicitudes!\$M$" . ($startRow+1) . ":\$M$" . $lastStatRow2, 
            "Solicitudes!\$N$" . ($startRow+1) . ":\$N$" . $lastStatRow2,
            count($statsTipos)
        );
        $chart2->setTopLeftPosition('J' . ($startRow + 27));
        $chart2->setBottomRightPosition('R' . ($startRow + 42));
        $sheet->addChart($chart2);
        
        // =======================
        // GRÁFICO 3: LINEA DE TIEMPO (POR MES)
        // =======================
        
        // 1. Calcular datos por mes (1-12)
        $statsMeses = array_fill(1, 12, 0);
        foreach ($solicitudes as $solicitud) {
            $mes = (int)$solicitud->created_at->format('n');
            $statsMeses[$mes]++;
        }

        // 2. Escribir datos auxiliares en columnas P y Q
        $sheet->setCellValue('P' . $startRow, 'Mes');
        $sheet->setCellValue('Q' . $startRow, 'Cant.');

        $mesesNombres = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        
        $grafRow3 = $startRow + 1;
        for ($m = 1; $m <= 12; $m++) {
            $sheet->setCellValue('P' . $grafRow3, $mesesNombres[$m - 1]);
            $sheet->setCellValue('Q' . $grafRow3, $statsMeses[$m]);
            $grafRow3++;
        }

        // 3. Crear el gráfico de LÍNEA
        $lastStatRow3 = $grafRow3 - 1;
        
        // Usamos una lógica manual aquí porque es LINECHART, no BARCHART
        $catRange3 = "Solicitudes!\$P$" . ($startRow+1) . ":\$P$" . $lastStatRow3;
        $valRange3 = "Solicitudes!\$Q$" . ($startRow+1) . ":\$Q$" . $lastStatRow3;

        $category3 = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $catRange3, null, 12);
        $values3   = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, $valRange3, null, 12);

        $series3 = new DataSeries(
            DataSeries::TYPE_LINECHART, // <--- Tipo Línea
            DataSeries::GROUPING_STANDARD,
            range(0,0), [], [$category3], [$values3]
        );

        $layout3 = new Layout(); $layout3->setShowVal(true); // Mostrar valores en los puntos
        $plotArea3 = new \PhpOffice\PhpSpreadsheet\Chart\PlotArea($layout3, [$series3]);
        $legend3 = new Legend(Legend::POSITION_BOTTOM, null, false);
        $title3 = new Title('Tendencia Mensual');

        $chart3 = new Chart('chart_meses', $title3, $legend3, $plotArea3, true, 0, null, null);
        
        // Ubicación: Debajo de la tabla de datos principal (Aprox fila + 5)
        $chart3->setTopLeftPosition('A' . ($row + 3)); 
        $chart3->setBottomRightPosition('H' . ($row + 20)); // Ancho completo de la tabla

        $sheet->addChart($chart3);


        // =======================
        // Ajustes Finales
        // =======================
        $this->autoFitColumns($sheet, count($headings));
        
        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    // Helper para crear gráficos más limpio
    private function createChart($title, $catRange, $valRange, $count) {
        $category = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, $catRange, null, $count);
        $values   = new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, $valRange, null, $count);
        
        $series = new DataSeries(
            DataSeries::TYPE_BARCHART, 
            DataSeries::GROUPING_CLUSTERED, 
            range(0,0), [], [$category], [$values]
        );
        $series->setPlotDirection(DataSeries::DIRECTION_COL);

        $layout = new Layout(); $layout->setShowVal(true);
        $plotArea = new \PhpOffice\PhpSpreadsheet\Chart\PlotArea($layout, [$series]);
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);
        $titleObj = new Title($title);

        return new Chart('chart_'.rand(), $titleObj, $legend, $plotArea, true, 0, null, null);
    }

    private function styleHeader($sheet, $colCount, $row)
    {
        $headerRange = 'A'.$row.':' . chr(64 + $colCount) . $row;
        $sheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('1E40AF');
        $sheet->getStyle($headerRange)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'))->setSize(11);
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->applyBorders($sheet, $headerRange);
    }

    private function styleData($sheet, $lastRow, $colCount, $startRow)
    {
        $dataRange = 'A'.$startRow.':' . chr(64 + $colCount) . $lastRow;
        $this->applyBorders($sheet, $dataRange);
        
        for ($r = $startRow; $r <= $lastRow; $r++) {
            if ($r % 2 === 0) {
                $sheet->getStyle('A'.$r.':' . chr(64 + $colCount) . $r)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F3F4F6');
            }
        }
    }

    private function applyBorders($sheet, $range)
    {
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('CCCCCC');
    }

    private function autoFitColumns($sheet, $colCount)
    {
        for ($col = 1; $col <= $colCount; $col++) {
            $sheet->getColumnDimension(chr(64 + $col))->setAutoSize(true);
        }
    }
}
