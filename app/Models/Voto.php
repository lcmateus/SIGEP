<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;   
use Illuminate\Notifications\Notifiable;                  
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voto extends Model
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'voto';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'integer';

    protected $fillable = [
        'usuario_id',
        'processo_id',
        'tipo',
        'justificativa',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function processo(): BelongsTo
    {
        return $this->belongsTo(Processo::class);
    }
}
