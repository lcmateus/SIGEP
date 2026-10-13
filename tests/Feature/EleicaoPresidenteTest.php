<?php

use App\Models\Etapa;
use App\Models\Processo;
use App\Models\RodadaVotacao;
use App\Models\UsuarioAdministrador;
use App\Models\UsuarioMembro;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function adminDeEleicao(): UsuarioAdministrador
{
    return UsuarioAdministrador::query()->create([
        'siape' => '9999999',
        'nome' => 'Secretario',
        'email' => 'secretario@sigep.test',
        'password' => bcrypt('SenhaForte#1234'),
    ]);
}

function membroDeEleicao(string $siape, bool $ativo = true, bool $presidente = false): UsuarioMembro
{
    return UsuarioMembro::query()->create([
        'siape' => $siape,
        'nome' => "Membro {$siape}",
        'email' => "membro{$siape}@sigep.test",
        'password' => bcrypt('SenhaForte#1234'),
        'data_ativacao' => $ativo ? now() : null,
        'ativado_por' => $ativo ? '9999999' : null,
        'is_presidente' => $presidente,
    ]);
}

function rodadaAbertaDeEleicao(string $siapeRelator, string $siapePresidente): RodadaVotacao
{
    $processo = Processo::query()->create([
        'numero_sei' => '0000000000'.random_int(1000, 9999),
        'data_admissao' => now()->toDateString(),
        'id_administrador' => '9999999',
        'id_relator' => $siapeRelator,
    ]);

    $etapa = Etapa::query()->create([
        'numero_sei_processo' => $processo->numero_sei,
        'ordem' => 1,
        'tipo' => Etapa::TIPO_JUIZO,
        'status' => Etapa::STATUS_EM_VOTACAO,
    ]);

    return RodadaVotacao::query()->create([
        'data_abertura' => now(),
        'data_encerramento' => now()->addWeek(),
        'resultado' => null,
        'id_etapa' => $etapa->id,
        'id_presidente' => $siapePresidente,
    ]);
}

it('elege um unico presidente e desmarca os demais', function () {
    $admin = adminDeEleicao();
    $atual = membroDeEleicao('1000001', true, true);
    $novo = membroDeEleicao('1000002');

    $this->actingAs($admin)
        ->patch(route('usuarios.eleger-presidente', $novo))
        ->assertRedirect(route('usuarios.list'));

    expect($atual->fresh()->is_presidente)->toBeFalse();
    expect($novo->fresh()->is_presidente)->toBeTrue();
});

it('reassina as rodadas abertas para o novo presidente ao eleger', function () {
    $admin = adminDeEleicao();
    membroDeEleicao('1000001', true, true);
    $novo = membroDeEleicao('1000002');
    membroDeEleicao('1000003');

    $aberta = rodadaAbertaDeEleicao('1000003', '1000001');

    $this->actingAs($admin)
        ->patch(route('usuarios.eleger-presidente', $novo));

    expect($aberta->fresh()->id_presidente)->toBe('1000002');
});

it('nao elege um membro pendente de aprovacao', function () {
    $admin = adminDeEleicao();
    $pendente = membroDeEleicao('1000003', false);

    $this->actingAs($admin)
        ->patch(route('usuarios.eleger-presidente', $pendente))
        ->assertRedirect()
        ->assertSessionHas('error');

    expect($pendente->fresh()->is_presidente)->toBeFalse();
});

it('bloqueia a eleicao para quem nao e administrador', function () {
    adminDeEleicao();
    $membro = membroDeEleicao('1000001');
    $alvo = membroDeEleicao('1000002');

    $this->actingAs($membro)
        ->patch(route('usuarios.eleger-presidente', $alvo))
        ->assertForbidden();
});
