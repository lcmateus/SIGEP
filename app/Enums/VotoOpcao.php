<?php

namespace App\Enums;

enum VotoOpcao: string
{
    case Favor = 'favor';
    case Contra = 'contra';
    case Abstencao = 'abstencao';
    case FavorComRessalvas = 'favor_com_ressalvas';
}