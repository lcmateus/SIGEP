<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ProcessoDevolvido extends Mailable
{
    public function __construct(
        public string $nome,
        public string $numeroSei,
        public string $relatorNome,
        public string $data,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'SIGEP - Processo devolvido à Secretaria');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.processo-devolvido');
    }
}