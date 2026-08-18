<?php

namespace App\Enums;

enum ResultadoVotacao: string
{
    case Aprovado = 'aprovado';
    case Reprovado = 'reprovado';
    case Pendente = 'pendente';
}