<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ProcessoReativado extends Mailable
{
    public function __construct(
        public string $nome,
        public string $numeroSei,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'SIGEP - Processo devolvido novamente a você');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.processo-reativado');
    }
}