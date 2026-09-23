<?php

use App\Models\Etapa;
use App\Models\Processo;
use App\Models\RodadaVotacao;
use App\Models\UsuarioAdministrador;
use App\Models\UsuarioMembro;
use App\Models\Voto;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function adminDeHistorico(): UsuarioAdministrador
{
    return UsuarioAdministrador::query()->create([
        'siape' => '9999999',
        'nome' => 'Secretario',
        'email' => 'secretario@sigep.test',
        'password' => bcrypt('SenhaForte#1234'),
    ]);
}

function membroDeHistorico(string $siape): UsuarioMembro
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

function processoDeHistorico(string $numeroSei, string $relatorSiape): Processo
{
    return Processo::query()->create([
        'numero_sei' => $numeroSei,
        'data_admissao' => now()->toDateString(),
        'id_administrador' => '9999999',
        'id_relator' => $relatorSiape,
    ]);
}

it('exibe o historico de votacoes agrupado por etapa com os votos de cada membro e o resultado', function () {
    adminDeHistorico();
    $presidente = membroDeHistorico('1000001');
    $m2 = membroDeHistorico('1000002');
    $m3 = membroDeHistorico('1000003');

    $processo = processoDeHistorico('00000000000016', '1000001');

    $etapaJuizo = Etapa::query()->create([
        'numero_sei_processo' => $processo->numero_sei,
        'ordem' => 1,
        'tipo' => Etapa::TIPO_JUIZO,
        'status' => Etapa::STATUS_FINALIZADO,
    ]);

    $etapaPP = Etapa::query()->create([
        'numero_sei_processo' => $processo->numero_sei,
        'ordem' => 2,
        'tipo' => Etapa::TIPO_PROCEDIMENTO_PRELIMINAR,
        'status' => Etapa::STATUS_FINALIZADO,
    ]);

    $rodada1 = RodadaVotacao::query()->create([
        'data_abertura' => now()->subDays(10),
        'data_encerramento' => now()->subDays(3),
        'resultado' => 'reprovado',
        'id_etapa' => $etapaJuizo->id,
        'id_presidente' => $presidente->siape,
    ]);

    $rodada2 = RodadaVotacao::query()->create([
        'data_abertura' => now()->subDays(2),
        'data_encerramento' => now()->subDay(),
        'resultado' => 'aprovado',
        'id_etapa' => $etapaPP->id,
        'id_presidente' => $presidente->siape,
    ]);

    Voto::query()->create([
        'opcao' => 'aprova',
        'justificativa' => null,
        'is_minerva' => false,
        'id_membro' => $m2->siape,
        'id_rodada' => $rodada1->id,
    ]);

    Voto::query()->create([
        'opcao' => 'desaprova',
        'justificativa' => null,
        'is_minerva' => false,
        'id_membro' => $m3->siape,
        'id_rodada' => $rodada1->id,
    ]);

    Voto::query()->create([
        'opcao' => 'aprova',
        'justificativa' => null,
        'is_minerva' => true,
        'id_membro' => $presidente->siape,
        'id_rodada' => $rodada1->id,
    ]);

    Voto::query()->create([
        'opcao' => 'abstenho',
        'justificativa' => 'nao tenho elementos suficientes',
        'is_minerva' => false,
        'id_membro' => $m2->siape,
        'id_rodada' => $rodada2->id,
    ]);

    Voto::query()->create([
        'opcao' => 'aprova com resalva',
        'justificativa' => 'ressalva registrada',
        'is_minerva' => false,
        'id_membro' => $m3->siape,
        'id_rodada' => $rodada2->id,
    ]);

    $this->actingAs($presidente)
        ->get(route('processos.show', $processo))
        ->assertOk()
        ->assertSee('Etapa atual: ' . Etapa::TIPO_PROCEDIMENTO_PRELIMINAR)
        ->assertSee(Etapa::TIPO_JUIZO)
        ->assertSee('Membro 1000002')
        ->assertSee('Membro 1000003')
        ->assertSee('Aprovou')
        ->assertSee('Reprovou')
        ->assertSee('Aprovou com ressalva')
        ->assertSee('Abstenção')
        ->assertSee('Minerva')
        ->assertSee('Aprovado')
        ->assertSee('Reprovado');
});

it('tambem exibe os votos anonimizados de membros excluidos no historico', function () {
    adminDeHistorico();
    $presidente = membroDeHistorico('1000001');

    $processo = processoDeHistorico('00000000000017', '1000001');

    $etapaJuizo = Etapa::query()->create([
        'numero_sei_processo' => $processo->numero_sei,
        'ordem' => 1,
        'tipo' => Etapa::TIPO_JUIZO,
        'status' => Etapa::STATUS_FINALIZADO,
    ]);

    $rodada = RodadaVotacao::query()->create([
        'data_abertura' => now()->subDays(5),
        'data_encerramento' => now()->subDay(),
        'resultado' => 'aprovado',
        'id_etapa' => $etapaJuizo->id,
        'id_presidente' => $presidente->siape,
    ]);

    Voto::query()->create([
        'opcao' => 'aprova',
        'justificativa' => null,
        'is_minerva' => false,
        'id_membro' => null,
        'id_rodada' => $rodada->id,
    ]);

    $this->actingAs($presidente)
        ->get(route('processos.show', $processo))
        ->assertOk()
        ->assertSee('Membro excluído (anonimizado)');
});