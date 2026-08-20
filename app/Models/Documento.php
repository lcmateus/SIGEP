<?php
// app/Models/Documento.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'documento';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'integer';

    protected $fillable = [
        'titulo',
        'descricao',
        'caminho',
        'referencia_tipo',
        'referencia_id',
        'tipo',
        'upload_feito_por',
        'data_upload',
    ];

    protected $casts = [
        'data_upload' => 'datetime',
    ];

    // Relacionamento polimórfico (documento pertence a um processo ou uma etapa)
    public function referencia()
    {
        return $this->morphTo('referencia', 'referencia_tipo', 'referencia_id');
    }

    // Relacionamento com o usuário que fez o upload
    public function usuario()
    {
        return $this->belongsTo(User::class, 'upload_feito_por', 'siape');
    }

    // Métodos auxiliares para verificar o tipo
    public function isMinuta()
    {
        return $this->tipo === 'minuta_acpp';
    }

    public function isVersaoFinal()
    {
        return $this->tipo === 'versao_final_acpp';
    }

    public function isVersaoAssinada()
    {
        return $this->tipo === 'versao_assinada_acpp';
    }

    public function isDiligencia()
    {
        return $this->tipo === 'diligencia';
    }

    public function isProva()
    {
        return in_array($this->tipo, ['prova_documental', 'prova_testemunhal', 'prova_pericial']);
    }

    public function isRelatorioParcial()
    {
        return $this->tipo === 'relatorio_parcial_pae';
    }

    public function isDefesa()
    {
        return $this->tipo === 'defesa_investigado';
    }

    public function isAlegoesFinais()
    {
        return $this->tipo === 'alegacoes_finais';
    }

    public function isReconsideracao()
    {
        return $this->tipo === 'reconsideracao';
    }

    // Retorna o nome legível do tipo (para exibir na view)
    public function getTipoDisplayAttribute()
    {
        return match($this->tipo) {
            'pdf_sei' => 'PDF do SEI',
            'relatorio_juizo' => 'Relatório do Juízo de Admissibilidade',
            'relatorio_pp' => 'Relatório do Procedimento Preliminar',
            'minuta_acpp' => 'Minuta do ACPP',
            'versao_final_acpp' => 'Versão Final do ACPP',
            'versao_assinada_acpp' => 'Versão Assinada do ACPP',
            'diligencia' => 'Diligência',
            'prova_documental' => 'Prova Documental',
            'prova_testemunhal' => 'Prova Testemunhal',
            'prova_pericial' => 'Prova Pericial',
            'relatorio_parcial_pae' => 'Relatório Parcial do PAE',
            'defesa_investigado' => 'Defesa do Investigado',
            'alegacoes_finais' => 'Alegações Finais',
            'reconsideracao' => 'Pedido de Reconsideração',
            'outro' => 'Outro Documento',
            default => $this->tipo
        };
    }

    // URL para acessar o arquivo
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->caminho);
    }

    // Verifica se o arquivo existe
    public function arquivoExiste()
    {
        return \Storage::disk('public')->exists($this->caminho);
    }
}