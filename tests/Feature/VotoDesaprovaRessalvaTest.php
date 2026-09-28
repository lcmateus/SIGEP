<?php

use App\Models\Etapa;
use App\Models\Processo;
use App\Models\RodadaVotacao;
use App\Models\UsuarioAdministrador;
use App\Models\UsuarioMembro;
use App\Models\Voto;
use App\Services\VotacaoService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function adminDeRessalvaDesaprova(): UsuarioAdministrador
{
    return UsuarioAdministrador::query()->create([
        'siape' => '9999999',
        'nome' => 'Secretario',
        'email' => 'secretario@sigep.test',
        'password' => bcrypt('SenhaForte#1234'),
    ]);
}

function membroDeRessalvaDesaprova(string $siape): UsuarioMembro
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

function rodadaDeRessalvaDesaprova(string $numeroSei, string $relatorSiape): RodadaVotacao
{
    $processo = Processo::query()->create([
        'numero_sei' => $numeroSei,
        'data_admissao' => now()->toDateString(),
        'id_administrador' => '9999999',
        'id_relator' => $relatorSiape,
    ]);

    $etapa = Etapa::query()->create([
        'numero_sei_processo' => $processo->numero_sei,
        'ordem' => 1,
        'tipo' => Etapa::TIPO_JUIZO,
        'status' => Etapa::STATUS_EM_ELABORACAO,
    ]);

    return RodadaVotacao::query()->create([
        'data_abertura' => now(),
        'data_encerramento' => now()->addWeek(),
        'resultado' => null,
        'id_etapa' => $etapa->id,
    ]);
}

it('conta o voto "desaprova com resalva" junto dos votos de desaprova', function () {
    adminDeRessalvaDesaprova();
    membroDeRessalvaDesaprova('1000001');
    membroDeRessalvaDesaprova('1000002');
    membroDeRessalvaDesaprova('1000003');

    $rodada = rodadaDeRessalvaDesaprova('00000000000020', '1000001');

    Voto::query()->create([
        'opcao' => 'aprova',
        'is_minerva' => false,
        'id_membro' => '1000001',
        'id_rodada' => $rodada->id,
    ]);

    Voto::query()->create([
        'opcao' => 'desaprova com resalva',
        'justificativa' => 'nao concordo com o parecer',
        'is_minerva' => false,
        'id_membro' => '1000002',
        'id_rodada' => $rodada->id,
    ]);

    Voto::query()->create([
        'opcao' => 'desaprova',
        'is_minerva' => false,
        'id_membro' => '1000003',
        'id_rodada' => $rodada->id,
    ]);

    $service = app(VotacaoService::class);

    expect($service->contagemVotos($rodada))->toBe([
        'aprova' => 1,
        'desaprova' => 2,
        'abstencoes' => 0,
    ]);
});

it('reprova a rodada quando desaprova com resalva forma a maioria', function () {
    adminDeRessalvaDesaprova();
    membroDeRessalvaDesaprova('1000001');
    membroDeRessalvaDesaprova('1000002');
    membroDeRessalvaDesaprova('1000003');

    $rodada = rodadaDeRessalvaDesaprova('00000000000021', '1000001');

    Voto::query()->create([
        'opcao' => 'aprova',
        'is_minerva' => false,
        'id_membro' => '1000001',
        'id_rodada' => $rodada->id,
    ]);

    Voto::query()->create([
        'opcao' => 'desaprova com resalva',
        'is_minerva' => false,
        'id_membro' => '1000002',
        'id_rodada' => $rodada->id,
    ]);

    Voto::query()->create([
        'opcao' => 'desaprova com resalva',
        'is_minerva' => false,
        'id_membro' => '1000003',
        'id_rodada' => $rodada->id,
    ]);

    $service = app(VotacaoService::class);

    expect($service->apurar($rodada))->toBe(VotacaoService::REPROVADO);
    expect($rodada->fresh()->resultado)->toBe('reprovado');
});

it('registra o voto "desaprova com resalva" pela rota de votacao', function () {
    adminDeRessalvaDesaprova();
    $votante = membroDeRessalvaDesaprova('1000001');
    membroDeRessalvaDesaprova('1000002');
    membroDeRessalvaDesaprova('1000003');

    $rodada = rodadaDeRessalvaDesaprova('00000000000022', '1000001');

    $this->actingAs($votante)
        ->post(route('votacoes.votar.store', $rodada), [
            'opcao' => 'desaprova com resalva',
            'justificativa' => 'ressalva registrada pela interface',
        ])
        ->assertRedirect(route('votacoes.disponiveis'));

    $this->assertDatabaseHas('voto', [
        'opcao' => 'desaprova com resalva',
        'justificativa' => 'ressalva registrada pela interface',
        'id_membro' => '1000001',
        'id_rodada' => $rodada->id,
    ]);
});