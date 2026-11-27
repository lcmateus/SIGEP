<?php

use App\Models\Documento;
use App\Models\Etapa;
use App\Models\Processo;
use App\Models\RodadaVotacao;
use App\Models\UsuarioAdministrador;
use App\Models\UsuarioMembro;
use App\Services\VotacaoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

function adminDeMediacao(): UsuarioAdministrador
{
    return UsuarioAdministrador::query()->firstOrCreate(
        ['siape' => '9999999'],
        [
            'nome' => 'Secretario',
            'email' => 'secretario@sigep.test',
            'password' => bcrypt('SenhaForte#1234'),
        ]
    );
}

function membroDeMediacao(string $siape, bool $presidente = false): UsuarioMembro
{
    return UsuarioMembro::query()->firstOrCreate(
        ['siape' => $siape],
        [
            'nome' => "Membro {$siape}",
            'email' => "membro{$siape}@sigep.test",
            'password' => bcrypt('SenhaForte#1234'),
            'data_ativacao' => now(),
            'ativado_por' => '9999999',
            'is_presidente' => $presidente,
        ]
    );
}

function baseDeMediacao(string $numeroSei): array
{
    adminDeMediacao();
    $relator = membroDeMediacao('1000001');

    $processo = Processo::query()->create([
        'numero_sei' => $numeroSei,
        'data_admissao' => now()->toDateString(),
        'id_administrador' => '9999999',
        'id_relator' => $relator->siape,
    ]);

    return [$relator, $processo];
}

function etapaDeMediacao(Processo $processo, int $ordem, string $tipo, string $status): Etapa
{
    return Etapa::query()->create([
        'numero_sei_processo' => $processo->numero_sei,
        'ordem' => $ordem,
        'tipo' => $tipo,
        'status' => $status,
    ]);
}

function cenarioAposProcedimentoPreliminar(string $numeroSei): array
{
    [$relator, $processo] = baseDeMediacao($numeroSei);

    etapaDeMediacao($processo, 1, Etapa::TIPO_JUIZO, Etapa::STATUS_FINALIZADO);
    etapaDeMediacao($processo, 2, Etapa::TIPO_PROCEDIMENTO_PRELIMINAR, Etapa::STATUS_FINALIZADO);

    return [$relator, $processo];
}

function cenarioComMediacao(string $numeroSei, string $status = Etapa::STATUS_EM_ELABORACAO): array
{
    [$relator, $processo] = cenarioAposProcedimentoPreliminar($numeroSei);
    $etapa = etapaDeMediacao($processo, 3, Etapa::TIPO_MEDIACAO, $status);

    return [$relator, $processo, $etapa];
}

afterEach(function () {
    foreach (Documento::query()->pluck('caminho') as $caminho) {
        $arquivoCompleto = public_path($caminho);
        if (file_exists($arquivoCompleto)) {
            unlink($arquivoCompleto);
        }
    }
});

it('registra a mediacao entre os tipos de etapa', function () {
    expect(Etapa::TIPOS)->toContain(Etapa::TIPO_MEDIACAO);

    [, $processo] = baseDeMediacao('00000000000050');
    $etapa = etapaDeMediacao($processo, 1, Etapa::TIPO_MEDIACAO, Etapa::STATUS_EM_ELABORACAO);

    expect($etapa->fresh()->tipo)->toBe(Etapa::TIPO_MEDIACAO);
});

it('oferece mediacao somente apos o procedimento preliminar', function () {
    $service = app(VotacaoService::class);

    [, $processoPp] = baseDeMediacao('00000000000051');
    etapaDeMediacao($processoPp, 1, Etapa::TIPO_PROCEDIMENTO_PRELIMINAR, Etapa::STATUS_FINALIZADO);

    expect($service->opcoesProximaEtapa($processoPp))->toBe([
        Etapa::TIPO_MEDIACAO,
        Etapa::TIPO_ACPP,
        Etapa::TIPO_PAE,
    ]);

    [, $processoJuizo] = baseDeMediacao('00000000000052');
    etapaDeMediacao($processoJuizo, 1, Etapa::TIPO_JUIZO, Etapa::STATUS_FINALIZADO);

    expect($service->opcoesProximaEtapa($processoJuizo))->not->toContain(Etapa::TIPO_MEDIACAO);

    [, $processoMediacao] = baseDeMediacao('00000000000053');
    etapaDeMediacao($processoMediacao, 1, Etapa::TIPO_MEDIACAO, Etapa::STATUS_FINALIZADO);

    expect($service->opcoesProximaEtapa($processoMediacao))->toBe([
        Etapa::TIPO_ACPP,
        Etapa::TIPO_PAE,
    ]);

    [, $processoAcpp] = baseDeMediacao('00000000000054');
    etapaDeMediacao($processoAcpp, 1, Etapa::TIPO_ACPP, Etapa::STATUS_FINALIZADO);

    expect($service->opcoesProximaEtapa($processoAcpp))->not->toContain(Etapa::TIPO_MEDIACAO);
});

it('exibe a mediacao na tela de proxima etapa apos o procedimento preliminar', function () {
    [$relator, $processo] = cenarioAposProcedimentoPreliminar('00000000000055');

    $this->actingAs($relator)
        ->get(route('processos.proxima-etapa', $processo))
        ->assertOk()
        ->assertSee(Etapa::TIPO_MEDIACAO)
        ->assertSee(Etapa::TIPO_ACPP)
        ->assertSee(Etapa::TIPO_PAE);
});

it('cria a etapa de mediacao como proxima etapa do procedimento preliminar', function () {
    [$relator, $processo] = cenarioAposProcedimentoPreliminar('00000000000056');

    $this->actingAs($relator)
        ->post(route('processos.proxima-etapa.store', $processo), [
            'tipo' => Etapa::TIPO_MEDIACAO,
        ])
        ->assertRedirect()
        ->assertSessionHas('status', 'Proxima etapa definida.');

    $mediacao = Etapa::query()->where('numero_sei_processo', $processo->numero_sei)
        ->where('ordem', 3)
        ->firstOrFail();

    expect($mediacao->tipo)->toBe(Etapa::TIPO_MEDIACAO);
    expect($mediacao->status)->toBe(Etapa::STATUS_EM_ELABORACAO);
});

it('nao exibe a mediacao apos o juizo', function () {
    [$relator, $processo] = baseDeMediacao('00000000000057');
    etapaDeMediacao($processo, 1, Etapa::TIPO_JUIZO, Etapa::STATUS_FINALIZADO);

    $this->actingAs($relator)
        ->get(route('processos.proxima-etapa', $processo))
        ->assertOk()
        ->assertDontSee(Etapa::TIPO_MEDIACAO)
        ->assertSee(Etapa::TIPO_PROCEDIMENTO_PRELIMINAR);
});

it('permite anexar documento e votar na mediacao', function () {
    [$relator, $processo, $etapa] = cenarioComMediacao('00000000000058');
    membroDeMediacao('1000002', true);

    $this->actingAs($relator)
        ->get(route('processos.show', $processo))
        ->assertOk()
        ->assertSee('Iniciar Votação')
        ->assertSee('Anexar documento');

    $this->actingAs($relator)
        ->post(route('documentos.store'), [
            'arquivo' => UploadedFile::fake()->create('mediacao.pdf', 100, 'application/pdf'),
            'etapa_id' => $etapa->id,
            'tipo' => 'relatorio_juizo',
        ])
        ->assertOk()
        ->assertJson(['ok' => true]);

    $this->actingAs($relator)
        ->put(route('processos.iniciar-votacao', $processo))
        ->assertRedirect(route('dashboard'));

    expect(Documento::query()->where('etapa_id', $etapa->id)->count())->toBe(1);
    expect($etapa->fresh()->status)->toBe(Etapa::STATUS_EM_VOTACAO);
    expect(RodadaVotacao::query()->where('id_etapa', $etapa->id)->exists())->toBeTrue();
});
