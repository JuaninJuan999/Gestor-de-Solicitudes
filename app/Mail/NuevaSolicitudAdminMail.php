<?php

namespace App\Mail;

use App\Models\Solicitud;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NuevaSolicitudAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public $solicitud;

    public function __construct(Solicitud $solicitud)
    {
        $this->solicitud = $solicitud;
    }

    public function build()
    {
        return $this->subject('Nueva solicitud: ' . ($this->solicitud->consecutivo ?? 'Sin consecutivo'))
                    ->markdown('emails.nueva-solicitud-admin');
    }
}
