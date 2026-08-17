<?php

namespace App\Enums;

enum DocumentoTipo: string
{
    case Relatorio = 'relatorio';
    case Diligencia = 'diligencia';
    case ElementoProva = 'elemento_prova';
    case Outro = 'outro';
}
