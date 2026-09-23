<?php

namespace App\Rules;

use App\Models\UsuarioAdministrador;
use App\Models\UsuarioMembro;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EmailUnico implements ValidationRule
{
    public function __construct(private ?string $ignorar = null)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $existe = UsuarioMembro::query()
            ->where('email', $value)
            ->whereNotNull('email')
            ->when($this->ignorar && $this->ignorar !== '', fn ($query) => $query->where('siape', '!=', $this->ignorar))
            ->exists()
            || UsuarioAdministrador::query()
                ->where('email', $value)
                ->when($this->ignorar && $this->ignorar !== '', fn ($query) => $query->where('siape', '!=', $this->ignorar))
                ->exists();

        if ($existe) {
            $fail('Este e-mail já está em uso.');
        }
    }
}