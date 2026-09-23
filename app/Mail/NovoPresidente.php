<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NovoPresidente extends Mailable
{
    public function __construct(
        public string $nome,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'SIGEP - Voce e o novo presidente');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.novo-presidente');
    }
}