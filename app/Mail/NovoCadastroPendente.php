<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NovoCadastroPendente extends Mailable
{
    public function __construct(
        public string $nome,
        public string $siape,
        public string $email,
        public string $data,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'SIGEP - Novo cadastro de membro aguardando aprovação');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.novo-cadastro-pendente');
    }
}