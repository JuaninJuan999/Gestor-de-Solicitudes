<?php

namespace App\Exports;

use App\Models\Solicitud;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
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

        // Encabezados
        $headings = [
            'Consecutivo',
            'Tipo',
            'Estado',
            'Usuario',
            'Área Solicitante',
            'Centro Costos',
            'Fecha Creación',
            'Items Total',
        ];

        $col = 1;
        foreach ($headings as $heading) {
            $sheet->setCellValueByColumnAndRow($col, 1, $heading);
            $col++;
        }

        // Datos
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

        $row = 2;
        foreach ($solicitudes as $solicitud) {
            $sheet->setCellValueByColumnAndRow(1, $row, $solicitud->consecutivo ?? 'N/A');
            $sheet->setCellValueByColumnAndRow(2, $row, ucwords(str_replace('_', ' ', $solicitud->tipo_solicitud)));
            $sheet->setCellValueByColumnAndRow(3, $row, ucfirst($solicitud->estado));
            $sheet->setCellValueByColumnAndRow(4, $row, $solicitud->user->name ?? 'N/A');
            $sheet->setCellValueByColumnAndRow(5, $row, $solicitud->area_solicitante ?? '');
            $sheet->setCellValueByColumnAndRow(6, $row, $solicitud->centro_costos ?? '');
            $sheet->setCellValueByColumnAndRow(7, $row, $solicitud->created_at->format('d/m/Y H:i'));
            $sheet->setCellValueByColumnAndRow(8, $row, $solicitud->items->count());
            $row++;
        }

        // Estilo básico de encabezados
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment;filename="'.$fileName.'"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }
}
