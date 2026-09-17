<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class CadastroAprovado extends Mailable
{
    public function __construct(
        public string $nome,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'SIGEP - Cadastro aprovado');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.cadastro-aprovado');
    }
}