<?php

use App\Models\Documento;
use App\Models\Etapa;
use App\Models\Processo;
use App\Models\RodadaVotacao;
use App\Models\UsuarioAdministrador;
use App\Models\UsuarioMembro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

function adminDeJuizoComum(): UsuarioAdministrador
{
    return UsuarioAdministrador::query()->create([
        'siape' => '9999999',
        'nome' => 'Secretario',
        'email' => 'secretario@sigep.test',
        'password' => bcrypt('SenhaForte#1234'),
    ]);
}

function membroDeJuizoComum(string $siape, bool $presidente = false): UsuarioMembro
{
    return UsuarioMembro::query()->create([
        'siape' => $siape,
        'nome' => "Membro {$siape}",
        'email' => "membro{$siape}@sigep.test",
        'password' => bcrypt('SenhaForte#1234'),
        'data_ativacao' => now(),
        'ativado_por' => '9999999',
        'is_presidente' => $presidente,
    ]);
}

function cenarioDeJuizoComum(string $numeroSei, string $status = Etapa::STATUS_EM_ELABORACAO): array
{
    adminDeJuizoComum();
    $relator = membroDeJuizoComum('1000001');

    $processo = Processo::query()->create([
        'numero_sei' => $numeroSei,
        'data_admissao' => now()->toDateString(),
        'id_administrador' => '9999999',
        'id_relator' => $relator->siape,
    ]);

    $etapa = Etapa::query()->create([
        'numero_sei_processo' => $processo->numero_sei,
        'ordem' => 1,
        'tipo' => Etapa::TIPO_JUIZO,
        'status' => $status,
    ]);

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

it('mostra acoes de etapa comum no juizo em elaboracao', function () {
    [$relator, $processo] = cenarioDeJuizoComum('00000000000040');

    $this->actingAs($relator)
        ->get(route('processos.show', $processo))
        ->assertOk()
        ->assertSee('Iniciar Votação')
        ->assertSee('Anexar documento')
        ->assertDontSee('Aceitar Processo?');
});

it('permite iniciar votacao no juizo', function () {
    [$relator, $processo, $etapa] = cenarioDeJuizoComum('00000000000041');
    membroDeJuizoComum('1000002', true);

    $this->actingAs($relator)
        ->put(route('processos.iniciar-votacao', $processo))
        ->assertRedirect(route('dashboard'));

    expect($etapa->fresh()->status)->toBe(Etapa::STATUS_EM_VOTACAO);
    expect(RodadaVotacao::query()->where('id_etapa', $etapa->id)->exists())->toBeTrue();
});

it('permite anexar documento no juizo', function () {
    [$relator, $processo, $etapa] = cenarioDeJuizoComum('00000000000042');

    $resposta = $this->actingAs($relator)
        ->post(route('documentos.store'), [
            'arquivo' => UploadedFile::fake()->create('parecer.pdf', 100, 'application/pdf'),
            'etapa_id' => $etapa->id,
            'tipo' => 'relatorio_juizo',
        ]);

    $resposta->assertOk()->assertJson(['ok' => true]);
    expect(Documento::query()->where('etapa_id', $etapa->id)->count())->toBe(1);
});

it('oferece proxima etapa e devolucao apos a votacao do juizo', function () {
    [$relator, $processo] = cenarioDeJuizoComum('00000000000043', Etapa::STATUS_FINALIZADO);

    $this->actingAs($relator)
        ->get(route('processos.show', $processo))
        ->assertOk()
        ->assertSee('Definir próxima etapa')
        ->assertSee('Devolver ao(à) Secretário(a)')
        ->assertDontSee('Aceitar Processo?');

    $this->actingAs($relator)
        ->get(route('processos.proxima-etapa', $processo))
        ->assertOk()
        ->assertSee(Etapa::TIPO_PROCEDIMENTO_PRELIMINAR)
        ->assertSee(Etapa::TIPO_ACPP)
        ->assertSee(Etapa::TIPO_PAE);
});

it('cria o procedimento preliminar como proxima etapa do juizo', function () {
    [$relator, $processo] = cenarioDeJuizoComum('00000000000044', Etapa::STATUS_FINALIZADO);

    $this->actingAs($relator)
        ->post(route('processos.proxima-etapa.store', $processo), [
            'tipo' => Etapa::TIPO_PROCEDIMENTO_PRELIMINAR,
        ])
        ->assertRedirect()
        ->assertSessionHas('status', 'Proxima etapa definida.');

    $proxima = Etapa::query()->where('numero_sei_processo', $processo->numero_sei)
        ->where('ordem', 2)
        ->firstOrFail();

    expect($proxima->tipo)->toBe(Etapa::TIPO_PROCEDIMENTO_PRELIMINAR);
    expect($proxima->status)->toBe(Etapa::STATUS_EM_ELABORACAO);
});

it('devolve o juizo finalizado ao secretario', function () {
    [$relator, $processo, $etapa] = cenarioDeJuizoComum('00000000000045', Etapa::STATUS_FINALIZADO);

    $this->actingAs($relator)
        ->put(route('processos.devolver-secretario', $processo))
        ->assertRedirect()
        ->assertSessionHas('status', 'Processo devolvido ao Secretario Geral.');

    expect($etapa->fresh()->status)->toBe(Etapa::STATUS_DEVOLVIDO);
    expect($processo->fresh()->data_devolucao)->not->toBeNull();
});

it('mantem o fluxo padrao apos o procedimento preliminar', function () {
    adminDeJuizoComum();
    $relator = membroDeJuizoComum('1000001');

    $processo = Processo::query()->create([
        'numero_sei' => '00000000000046',
        'data_admissao' => now()->toDateString(),
        'id_administrador' => '9999999',
        'id_relator' => $relator->siape,
    ]);

    Etapa::query()->create([
        'numero_sei_processo' => $processo->numero_sei,
        'ordem' => 1,
        'tipo' => Etapa::TIPO_JUIZO,
        'status' => Etapa::STATUS_FINALIZADO,
    ]);

    Etapa::query()->create([
        'numero_sei_processo' => $processo->numero_sei,
        'ordem' => 2,
        'tipo' => Etapa::TIPO_PROCEDIMENTO_PRELIMINAR,
        'status' => Etapa::STATUS_FINALIZADO,
    ]);

    $this->actingAs($relator)
        ->get(route('processos.proxima-etapa', $processo))
        ->assertOk()
        ->assertSee(Etapa::TIPO_ACPP)
        ->assertSee(Etapa::TIPO_PAE)
        ->assertDontSee(Etapa::TIPO_PROCEDIMENTO_PRELIMINAR);
});
