<?php

namespace App\Enums;

enum UserTipo: string
{
    case Admin = 'admin';
    case Membro = 'membro';
}