<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class CodigoRecuperacao extends Mailable
{
    public function __construct(
        public string $nome,
        public string $codigo,
        public string $expiraEm,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'SIGEP - Codigo de recuperacao de senha');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.codigo-recuperacao');
    }
}