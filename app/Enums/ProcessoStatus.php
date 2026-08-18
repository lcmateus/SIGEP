<?php

namespace App\Enums;

enum ProcessoStatus: string
{
    case EmElaboracao = 'em_elaboracao';
    case EmVotacao = 'em_votacao';
    case AguardandoMinerva = 'aguardando_minerva';
    case VotacaoEncerrada = 'votacao_encerrada';
    case Concluido = 'concluido';
    case AguardandoDevolucaoSecretaria = 'aguardando_devolucao_secretaria';
    case Arquivado = 'arquivado';
}