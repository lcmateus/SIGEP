<?php

namespace App\Rules;

use App\Models\UsuarioAdministrador;
use App\Models\UsuarioMembro;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SiapeUnico implements ValidationRule
{
    public function __construct(private ?string $ignorar = null)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $existe = UsuarioMembro::query()
            ->where('siape', $value)
            ->when($this->ignorar, fn ($query) => $query->where('siape', '!=', $this->ignorar))
            ->exists()
            || UsuarioAdministrador::query()
                ->where('siape', $value)
                ->when($this->ignorar, fn ($query) => $query->where('siape', '!=', $this->ignorar))
                ->exists();

        if ($existe) {
            $fail('Este SIAPE já está em uso.');
        }
    }
}