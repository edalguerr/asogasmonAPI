<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    public $subject ='Reestablecimiento de contraseña';
    public $nombreDestinatario;
    public $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($nombre, $token_enlace)
    {
        //
        $this->nombreDestinatario = $nombre;
        $this->token = $token_enlace;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.resetPassword');
    }
}
