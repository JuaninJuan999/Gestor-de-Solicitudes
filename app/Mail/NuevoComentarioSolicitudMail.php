<?php

namespace App\Mail;

use App\Models\Solicitud;
use App\Models\Comentario;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NuevoComentarioSolicitudMail extends Mailable
{
    use Queueable, SerializesModels;

    public $solicitud;
    public $comentario;

    public function __construct(Solicitud $solicitud, Comentario $comentario)
    {
        $this->solicitud  = $solicitud;
        $this->comentario = $comentario;
    }

    public function build()
    {
        $mail = $this->subject('Nuevo comentario en la solicitud ' . ($this->solicitud->consecutivo ?? $this->solicitud->id))
                     ->view('emails.nuevo-comentario-solicitud');

        if ($this->comentario->archivo) {
            $mail->attach(storage_path('app/public/' . $this->comentario->archivo));
        }

        return $mail;
    }
}
