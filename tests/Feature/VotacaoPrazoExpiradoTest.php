<?php

use App\Models\Etapa;
use App\Models\Processo;
use App\Models\RodadaVotacao;
use App\Models\UsuarioAdministrador;
use App\Models\UsuarioMembro;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function adminParaPrazo(): UsuarioAdministrador
{
    return UsuarioAdministrador::query()->create([
        'siape' => '9999999',
        'nome' => 'Secretario',
        'email' => 'secretario@sigep.test',
        'password' => bcrypt('SenhaForte#1234'),
    ]);
}

function membroParaPrazo(string $siape): UsuarioMembro
{
    return UsuarioMembro::query()->create([
        'siape' => $siape,
        'nome' => "Membro {$siape}",
        'email' => "membro{$siape}@sigep.test",
        'password' => bcrypt('SenhaForte#1234'),
        'data_ativacao' => now(),
        'ativado_por' => '9999999',
    ]);
}

function rodadaParaPrazo(string $presidenteSiape, $encerramento): RodadaVotacao
{
    $processo = Processo::query()->create([
        'numero_sei' => '0000000000'.random_int(1000, 9999),
        'data_admissao' => now()->toDateString(),
        'id_administrador' => '9999999',
        'id_relator' => $presidenteSiape,
    ]);

    $etapa = Etapa::query()->create([
        'numero_sei_processo' => $processo->numero_sei,
        'ordem' => 1,
        'tipo' => Etapa::TIPO_JUIZO,
        'status' => Etapa::STATUS_EM_VOTACAO,
    ]);

    return RodadaVotacao::query()->create([
        'data_abertura' => now()->subWeek(),
        'data_encerramento' => $encerramento,
        'resultado' => null,
        'id_etapa' => $etapa->id,
        'id_presidente' => $presidenteSiape,
    ]);
}

it('nao lista votacoes cujo prazo ja expirou', function () {
    adminParaPrazo();
    $votante = membroParaPrazo('1000001');
    $expirada = rodadaParaPrazo($votante->siape, now()->subDay());
    $futura = rodadaParaPrazo($votante->siape, now()->addDay());

    $this->actingAs($votante)
        ->get(route('votacoes.disponiveis'))
        ->assertOk()
        ->assertViewHas('rodadas', function ($rodadas) use ($expirada, $futura) {
            $ids = $rodadas->pluck('id');

            return $ids->contains($futura->id) && ! $ids->contains($expirada->id);
        });
});

it('redireciona com mensagem clara ao tentar votar em rodada vencida', function () {
    adminParaPrazo();
    $votante = membroParaPrazo('1000001');
    $expirada = rodadaParaPrazo($votante->siape, now()->subDay());

    $this->actingAs($votante)
        ->get(route('votacoes.votar', $expirada))
        ->assertRedirect(route('votacoes.disponiveis'))
        ->assertSessionHas('error', 'O prazo desta votação foi encerrado.');
});

it('mantem o acesso as rodadas dentro do prazo', function () {
    adminParaPrazo();
    $votante = membroParaPrazo('1000001');
    $futura = rodadaParaPrazo($votante->siape, now()->addDay());

    $this->actingAs($votante)
        ->get(route('votacoes.votar', $futura))
        ->assertOk()
        ->assertViewIs('votacoes.votar');
});
