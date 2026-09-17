<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ProcessoDesignado extends Mailable
{
    public function __construct(
        public string $nome,
        public string $numeroSei,
        public string $etapaTipo,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'SIGEP - Novo processo designado para você');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.processo-designado');
    }
}