<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RelatorioSistema extends Model
{
    protected $fillable = [
        'user_id',
        'acao',
        'detalhes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
