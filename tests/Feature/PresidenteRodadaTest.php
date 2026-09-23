<?php

use App\Models\Etapa;
use App\Models\Processo;
use App\Models\RodadaVotacao;
use App\Models\UsuarioAdministrador;
use App\Models\UsuarioMembro;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function adminDeRodada(): UsuarioAdministrador
{
    return UsuarioAdministrador::query()->create([
        'siape' => '9999999',
        'nome' => 'Secretario',
        'email' => 'secretario@sigep.test',
        'password' => bcrypt('SenhaForte#1234'),
    ]);
}

function membroDeRodada(string $siape): UsuarioMembro
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

function etapaEmElaboracaoDeRodada(string $numeroSei, string $relatorSiape): Etapa
{
    $processo = Processo::query()->create([
        'numero_sei' => $numeroSei,
        'data_admissao' => now()->toDateString(),
        'id_administrador' => '9999999',
        'id_relator' => $relatorSiape,
    ]);

    return Etapa::query()->create([
        'numero_sei_processo' => $processo->numero_sei,
        'ordem' => 1,
        'tipo' => Etapa::TIPO_JUIZO,
        'status' => Etapa::STATUS_EM_ELABORACAO,
    ]);
}

function rodadaAguardandoMinervaDeRodada(Etapa $etapa, string $presidenteSiape): RodadaVotacao
{
    $etapa->update(['status' => Etapa::STATUS_AGUARDANDO_MINERVA]);

    return RodadaVotacao::query()->create([
        'data_abertura' => now(),
        'data_encerramento' => now()->addWeek(),
        'resultado' => null,
        'id_etapa' => $etapa->id,
        'id_presidente' => $presidenteSiape,
    ]);
}

it('atribui um presidente proprio a cada rodada ao iniciar votacao sem afetar a anterior', function () {
    adminDeRodada();
    $m1 = membroDeRodada('1000001');
    $m2 = membroDeRodada('1000002');

    $etapaA = etapaEmElaboracaoDeRodada('00000000000010', '1000001');
    $etapaB = etapaEmElaboracaoDeRodada('00000000000011', '1000002');

    $this->actingAs($m1)
        ->put(route('processos.iniciar-votacao', $etapaA->processo))
        ->assertRedirect();

    $rodada1 = RodadaVotacao::query()->where('id_etapa', $etapaA->id)->firstOrFail();
    $presidenteOriginal = $rodada1->id_presidente;
    expect($presidenteOriginal)->not->toBeNull();

    $this->actingAs($m2)
        ->put(route('processos.iniciar-votacao', $etapaB->processo))
        ->assertRedirect();

    $rodada2 = RodadaVotacao::query()->where('id_etapa', $etapaB->id)->firstOrFail();
    expect($rodada2->id_presidente)->not->toBeNull();

    expect($rodada1->fresh()->id_presidente)->toBe($presidenteOriginal);
});

it('somente o presidente da rodada pode registrar o voto de Minerva', function () {
    adminDeRodada();
    $presidente = membroDeRodada('1000001');
    $outro = membroDeRodada('1000002');

    $etapa = etapaEmElaboracaoDeRodada('00000000000012', '1000001');
    $rodada = rodadaAguardandoMinervaDeRodada($etapa, '1000001');

    $this->actingAs($outro)
        ->post(route('votacoes.minerva.votar', $rodada), ['opcao' => 'aprova'])
        ->assertForbidden();

    $this->actingAs($presidente)
        ->post(route('votacoes.minerva.votar', $rodada), ['opcao' => 'aprova'])
        ->assertRedirect();

    expect($rodada->fresh()->resultado)->toBe('aprovado');
    expect($etapa->fresh()->status)->toBe(Etapa::STATUS_FINALIZADO);
});

it('a tela de Minerva lista apenas as rodadas em que o usuario e presidente', function () {
    adminDeRodada();
    $presidente = membroDeRodada('1000001');
    $outro = membroDeRodada('1000002');

    $etapaP = etapaEmElaboracaoDeRodada('00000000000013', '1000001');
    $etapaO = etapaEmElaboracaoDeRodada('00000000000014', '1000002');
    $minha = rodadaAguardandoMinervaDeRodada($etapaP, '1000001');
    $outra = rodadaAguardandoMinervaDeRodada($etapaO, '1000002');

    $this->actingAs($presidente)
        ->get(route('votacoes.minerva'))
        ->assertOk()
        ->assertSee($minha->etapa->processo->numero_sei)
        ->assertDontSee($outra->etapa->processo->numero_sei);
});

it('a tela de Minerva para votar so e acessivel ao presidente da rodada', function () {
    adminDeRodada();
    $presidente = membroDeRodada('1000001');
    $outro = membroDeRodada('1000002');

    $etapa = etapaEmElaboracaoDeRodada('00000000000015', '1000001');
    $rodada = rodadaAguardandoMinervaDeRodada($etapa, '1000001');

    $this->actingAs($outro)
        ->get(route('votacoes.minerva.votar-pagina', $rodada))
        ->assertForbidden();

    $this->actingAs($presidente)
        ->get(route('votacoes.minerva.votar-pagina', $rodada))
        ->assertOk()
        ->assertSee($rodada->etapa->processo->numero_sei)
        ->assertSee('Aprovar')
        ->assertSee('Reprovar');
});