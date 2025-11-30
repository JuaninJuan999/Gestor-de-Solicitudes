<?php

namespace App\Mail;

use App\Models\Solicitud;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CambioEstadoSolicitudMail extends Mailable
{
    use Queueable, SerializesModels;

    public $solicitud;
    public $comentario;

    public function __construct(Solicitud $solicitud, ?string $comentario = null)
    {
        $this->solicitud = $solicitud;
        $this->comentario = $comentario;
    }

    public function build()
    {
        return $this->subject('Actualización de estado: ' . ($this->solicitud->consecutivo ?? 'Solicitud'))
                    ->markdown('emails.cambio-estado-solicitud');
    }
}
