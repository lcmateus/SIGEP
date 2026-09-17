<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NovaVotacao extends Mailable
{
    public function __construct(
        public string $nome,
        public string $numeroSei,
        public string $etapaTipo,
        public string $encerramento,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'SIGEP - Nova votação aberta');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.nova-votacao');
    }
}