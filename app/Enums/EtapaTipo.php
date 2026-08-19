<?php

namespace App\Enums;

enum EtapaTipo: string
{
    case JuizoAdmissibilidade = 'juizo_admissibilidade';
    case ProcedimentoPreliminar = 'procedimento_preliminar';
    case Relatoria = 'relatoria';
    case Arquivamento = 'arquivamento';
}