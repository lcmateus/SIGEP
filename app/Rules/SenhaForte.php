<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SenhaForte implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $senha = (string) $value;

        $valida = mb_strlen($senha) >= 12
            && preg_match('/[A-Z]/', $senha) === 1
            && preg_match('/[0-9]/', $senha) === 1
            && preg_match('/[^A-Za-z0-9]/', $senha) === 1;

        if (! $valida) {
            $fail('A senha deve ter pelo menos 12 caracteres, com pelo menos 1 letra maiúscula, 1 número e 1 caractere especial.');
        }
    }
}