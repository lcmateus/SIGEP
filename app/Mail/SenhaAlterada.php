<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class SenhaAlterada extends Mailable
{
    public function __construct(
        public string $nome,
        public string $data,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'SIGEP - Sua senha foi alterada');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.senha-alterada');
    }
}