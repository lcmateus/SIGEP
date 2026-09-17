<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class VotacaoEncerrada extends Mailable
{
    public function __construct(
        public string $nome,
        public string $numeroSei,
        public string $resultado,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'SIGEP - Votação encerrada');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.votacao-encerrada');
    }
}