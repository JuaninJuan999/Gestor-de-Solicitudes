<?php

namespace App\Exports;

use App\Models\Solicitud;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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

    /**
     * Genera y devuelve un StreamedResponse con el archivo Excel.
     */
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

        // Escribir encabezados (A1, B1, C1, ...)
        $colLetter = 'A';
        foreach ($headings as $heading) {
            $sheet->setCellValue($colLetter . '1', $heading);
            $colLetter++;
        }

        // Estilo de encabezados
        $this->styleHeader($sheet, count($headings));

        // Obtener datos con filtros
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

        // Llenar datos (A2, B2, C2, ...)
        $row = 2;
        foreach ($solicitudes as $solicitud) {
            $sheet->setCellValue('A' . $row, $solicitud->consecutivo ?? 'N/A');
            $sheet->setCellValue('B' . $row, ucwords(str_replace('_', ' ', $solicitud->tipo_solicitud)));
            $sheet->setCellValue('C' . $row, ucfirst($solicitud->estado));
            $sheet->setCellValue('D' . $row, $solicitud->user->name ?? 'N/A');
            $sheet->setCellValue('E' . $row, $solicitud->user->area ?? 'N/A'); // Área del usuario
            $sheet->setCellValue('F' . $row, $solicitud->centro_costos ?? '');
            $sheet->setCellValue('G' . $row, $solicitud->created_at->format('d/m/Y H:i'));
            $sheet->setCellValue('H' . $row, $solicitud->items->count());
            $row++;
        }

        // Aplicar estilos a los datos
        $this->styleData($sheet, $row - 1, count($headings));

        // Ajustar ancho de columnas
        $this->autoFitColumns($sheet, count($headings));

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename="' . $fileName . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    /**
     * Estiliza la fila de encabezados.
     */
    private function styleHeader($sheet, $colCount)
    {
        $headerRange = 'A1:' . chr(64 + $colCount) . '1';

        // Fondo azul
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('1E40AF'); // Azul oscuro

        // Letra blanca y bold
        $sheet->getStyle($headerRange)->getFont()
            ->setBold(true)
            ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'))
            ->setSize(11);

        // Centrado vertical y horizontal
        $sheet->getStyle($headerRange)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        // Altura de fila
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Bordes
        $this->applyBorders($sheet, $headerRange);
    }

    /**
     * Estiliza las filas de datos.
     */
    private function styleData($sheet, $lastRow, $colCount)
    {
        $dataRange = 'A2:' . chr(64 + $colCount) . $lastRow;

        // Bordes para todas las celdas
        $this->applyBorders($sheet, $dataRange);

        // Alineación
        $sheet->getStyle($dataRange)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // Alternar colores de fila (zebra striping)
        for ($row = 2; $row <= $lastRow; $row++) {
            $rowRange = 'A' . $row . ':' . chr(64 + $colCount) . $row;

            if ($row % 2 === 0) {
                $sheet->getStyle($rowRange)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F3F4F6'); // Gris claro
            }

            // Altura de fila
            $sheet->getRowDimension($row)->setRowHeight(20);
        }

        // Fuente
        $sheet->getStyle($dataRange)->getFont()->setSize(10);
    }

    /**
     * Aplica bordes a un rango de celdas.
     */
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

    /**
     * Ajusta automáticamente el ancho de las columnas.
     */
    private function autoFitColumns($sheet, $colCount)
    {
        for ($col = 1; $col <= $colCount; $col++) {
            $column = chr(64 + $col);
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }
}
