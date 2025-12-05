<?php

namespace App\Exports;

use App\Models\Solicitud;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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

        // Encabezados
        $headings = [
            'Consecutivo',
            'Tipo',
            'Estado',
            'Usuario',
            'Área',
            'Centro Costos',
            'Fecha Creación',
            'Items',
        ];

        // Encabezados A1:H1
        $colLetter = 'A';
        foreach ($headings as $heading) {
            $sheet->setCellValue($colLetter . '1', $heading);
            $colLetter++;
        }

        $this->styleHeader($sheet, count($headings));

        // Datos con filtros
        $query = Solicitud::with('user');

        if ($this->fechaInicio) {
            $query->whereDate('created_at', '>=', $this->fechaInicio);
        }
        if ($this->fechaFin) {
            $query->whereDate('created_at', '<=', $this->fechaFin);
        }
        if ($this->estado) {
            $query->where('estado', $this->estado);
        }
        if ($this->tipoSolicitud) {
            $query->where('tipo_solicitud', $this->tipoSolicitud);
        }

        $solicitudes = $query->orderBy('created_at', 'desc')->get();

        // Llenar tabla principal
        $row = 2;
        foreach ($solicitudes as $solicitud) {
            $sheet->setCellValue('A' . $row, $solicitud->consecutivo ?? 'N/A');
            $sheet->setCellValue('B' . $row, ucwords(str_replace('_', ' ', $solicitud->tipo_solicitud)));
            $sheet->setCellValue('C' . $row, ucfirst($solicitud->estado));
            $sheet->setCellValue('D' . $row, $solicitud->user->name ?? 'N/A');
            $sheet->setCellValue('E' . $row, $solicitud->user->area ?? 'N/A');
            $sheet->setCellValue('F' . $row, $solicitud->centro_costos ?? '');
            $sheet->setCellValue('G' . $row, $solicitud->created_at->format('d/m/Y H:i'));
            $sheet->setCellValue('H' . $row, $solicitud->items->count());
            $row++;
        }

        $this->styleData($sheet, $row - 1, count($headings));

        // =======================
        // Gráfico 1: por estado
        // =======================
        $statsEstados = [
            'Pendiente'  => $solicitudes->where('estado', 'pendiente')->count(),
            'En Proceso' => $solicitudes->where('estado', 'en_proceso')->count(),
            'Finalizada' => $solicitudes->where('estado', 'finalizada')->count(),
            'Rechazada'  => $solicitudes->where('estado', 'rechazada')->count(),
        ];

        $sheet->setCellValue('J1', 'Estado');
        $sheet->setCellValue('K1', 'Cantidad');

        $grafRow = 2;
        foreach ($statsEstados as $estadoNombre => $cantidad) {
            $sheet->setCellValue('J' . $grafRow, $estadoNombre);
            $sheet->setCellValue('K' . $grafRow, $cantidad);
            $grafRow++;
        }

        $categoryRange1 = new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_STRING,
            'Solicitudes!$J$2:$J$5',
            null,
            4
        );

        $valuesRange1 = new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_NUMBER,
            'Solicitudes!$K$2:$K$5',
            null,
            4
        );

        $series1 = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            range(0, 0),
            [],
            [$categoryRange1],
            [$valuesRange1]
        );
        $series1->setPlotDirection(DataSeries::DIRECTION_COL);

        $layout1   = new Layout();
        $layout1->setShowVal(true);
        $plotArea1 = new \PhpOffice\PhpSpreadsheet\Chart\PlotArea($layout1, [$series1]);
        $legend1   = new Legend(Legend::POSITION_RIGHT, null, false);
        $title1    = new Title('Solicitudes por estado');

        $chart1 = new Chart(
            'chart_estados',
            $title1,
            $legend1,
            $plotArea1,
            true,
            0,
            null,
            null
        );
        $chart1->setTopLeftPosition('J7');
        $chart1->setBottomRightPosition('R25');
        $sheet->addChart($chart1);

        // =======================
        // Gráfico 2: por tipo
        // =======================
        $statsTipos = [
            'Estándar'          => $solicitudes->where('tipo_solicitud', 'estandar')->count(),
            'Traslado Bodegas'  => $solicitudes->where('tipo_solicitud', 'traslado_bodegas')->count(),
            'Solicitud Pedidos' => $solicitudes->where('tipo_solicitud', 'solicitud_pedidos')->count(),
        ];

        $sheet->setCellValue('M1', 'Tipo Solicitud');
        $sheet->setCellValue('N1', 'Cantidad');

        $grafRow2 = 2;
        foreach ($statsTipos as $tipoNombre => $cantidad) {
            $sheet->setCellValue('M' . $grafRow2, $tipoNombre);
            $sheet->setCellValue('N' . $grafRow2, $cantidad);
            $grafRow2++;
        }

        $categoryRange2 = new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_STRING,
            'Solicitudes!$M$2:$M$4',
            null,
            3
        );

        $valuesRange2 = new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_NUMBER,
            'Solicitudes!$N$2:$N$4',
            null,
            3
        );

        $series2 = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            range(0, 0),
            [],
            [$categoryRange2],
            [$valuesRange2]
        );
        $series2->setPlotDirection(DataSeries::DIRECTION_COL);

        $layout2   = new Layout();
        $layout2->setShowVal(true);
        $plotArea2 = new \PhpOffice\PhpSpreadsheet\Chart\PlotArea($layout2, [$series2]);
        $legend2   = new Legend(Legend::POSITION_RIGHT, null, false);
        $title2    = new Title('Solicitudes por tipo');

        $chart2 = new Chart(
            'chart_tipos',
            $title2,
            $legend2,
            $plotArea2,
            true,
            0,
            null,
            null
        );
        $chart2->setTopLeftPosition('J27');
        $chart2->setBottomRightPosition('R45');
        $sheet->addChart($chart2);

        // ==========================
        // Gráfico 3: solicitudes/mes
        // ==========================
        // Inicializar meses 1–12
        $statsMeses = array_fill(1, 12, 0);
        foreach ($solicitudes as $solicitud) {
            $mes = (int)$solicitud->created_at->format('n');
            $statsMeses[$mes]++;
        }

        // Tabla auxiliar en P1:Q13
        $sheet->setCellValue('P1', 'Mes');
        $sheet->setCellValue('Q1', 'Cantidad');

        $mesesCortos = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];

        $grafRow3 = 2;
        for ($m = 1; $m <= 12; $m++) {
            $sheet->setCellValue('P' . $grafRow3, $mesesCortos[$m - 1]);
            $sheet->setCellValue('Q' . $grafRow3, $statsMeses[$m]);
            $grafRow3++;
        }

        $categoryRange3 = new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_STRING,
            'Solicitudes!$P$2:$P$13',
            null,
            12
        );

        $valuesRange3 = new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_NUMBER,
            'Solicitudes!$Q$2:$Q$13',
            null,
            12
        );

        $series3 = new DataSeries(
            DataSeries::TYPE_LINECHART,
            DataSeries::GROUPING_STANDARD,
            range(0, 0),
            [],
            [$categoryRange3],
            [$valuesRange3]
        );

        $layout3   = new Layout();
        $layout3->setShowVal(false);
        $plotArea3 = new \PhpOffice\PhpSpreadsheet\Chart\PlotArea($layout3, [$series3]);
        $legend3   = new Legend(Legend::POSITION_RIGHT, null, false);
        $title3    = new Title('Solicitudes por mes');

        $chart3 = new Chart(
            'chart_meses',
            $title3,
            $legend3,
            $plotArea3,
            true,
            0,
            null,
            null
        );
        $chart3->setTopLeftPosition('A' . ($row + 2));      // debajo de la tabla
        $chart3->setBottomRightPosition('I' . ($row + 22)); // ocupa ancho de tabla
        $sheet->addChart($chart3);

        // Ajustar ancho columnas principales
        $this->autoFitColumns($sheet, count($headings));

        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true); // imprescindible

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename="' . $fileName . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    private function styleHeader($sheet, $colCount)
    {
        $headerRange = 'A1:' . chr(64 + $colCount) . '1';

        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('1E40AF');

        $sheet->getStyle($headerRange)->getFont()
            ->setBold(true)
            ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'))
            ->setSize(11);

        $sheet->getStyle($headerRange)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->getRowDimension(1)->setRowHeight(25);

        $this->applyBorders($sheet, $headerRange);
    }

    private function styleData($sheet, $lastRow, $colCount)
    {
        $dataRange = 'A2:' . chr(64 + $colCount) . $lastRow;

        $this->applyBorders($sheet, $dataRange);

        $sheet->getStyle($dataRange)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);

        for ($row = 2; $row <= $lastRow; $row++) {
            $rowRange = 'A' . $row . ':' . chr(64 + $colCount) . $row;

            if ($row % 2 === 0) {
                $sheet->getStyle($rowRange)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F3F4F6');
            }

            $sheet->getRowDimension($row)->setRowHeight(20);
        }

        $sheet->getStyle($dataRange)->getFont()->setSize(10);
    }

    private function applyBorders($sheet, $range)
    {
        $borderStyle = [
            'borderStyle' => Border::BORDER_THIN,
            'color'       => ['rgb' => 'CCCCCC'],
        ];

        $sheet->getStyle($range)->getBorders()
            ->getLeft()->setBorderStyle($borderStyle['borderStyle'])->getColor()->setRGB($borderStyle['color']['rgb']);
        $sheet->getStyle($range)->getBorders()
            ->getRight()->setBorderStyle($borderStyle['borderStyle'])->getColor()->setRGB($borderStyle['color']['rgb']);
        $sheet->getStyle($range)->getBorders()
            ->getTop()->setBorderStyle($borderStyle['borderStyle'])->getColor()->setRGB($borderStyle['color']['rgb']);
        $sheet->getStyle($range)->getBorders()
            ->getBottom()->setBorderStyle($borderStyle['borderStyle'])->getColor()->setRGB($borderStyle['color']['rgb']);
    }

    private function autoFitColumns($sheet, $colCount)
    {
        for ($col = 1; $col <= $colCount; $col++) {
            $column = chr(64 + $col);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }
}
