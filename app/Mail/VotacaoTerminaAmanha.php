<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class VotacaoTerminaAmanha extends Mailable
{
    public function __construct(
        public string $nome,
        public string $numeroSei,
        public string $encerramento,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'SIGEP - Votação encerra amanhã');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.votacao-termina-amanha');
    }
}