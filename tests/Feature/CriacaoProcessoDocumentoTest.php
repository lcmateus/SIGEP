<?php

use App\Models\Documento;
use App\Models\Etapa;
use App\Models\Processo;
use App\Models\UsuarioAdministrador;
use App\Models\UsuarioMembro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class);

function adminDeCriacao(): UsuarioAdministrador
{
    return UsuarioAdministrador::query()->create([
        'siape' => '9999999',
        'nome' => 'Secretario',
        'email' => 'secretario@sigep.test',
        'password' => bcrypt('SenhaForte#1234'),
    ]);
}

function membroAtivoDeCriacao(string $siape): UsuarioMembro
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

afterEach(function () {
    foreach (Documento::query()->pluck('caminho') as $caminho) {
        $arquivoCompleto = public_path($caminho);
        if (file_exists($arquivoCompleto)) {
            unlink($arquivoCompleto);
        }
    }
});

it('bloqueia a criacao de processo sem documento obrigatorio', function () {
    $admin = adminDeCriacao();
    membroAtivoDeCriacao('1000001');

    $this->actingAs($admin)
        ->post(route('processos.store'), [
            'numero_sei' => '00000000000030',
        ])
        ->assertSessionHasErrors('arquivo');

    expect(Processo::query()->count())->toBe(0);
});

it('cria o processo, a primeira etapa e anexa o documento a ela', function () {
    $admin = adminDeCriacao();
    membroAtivoDeCriacao('1000001');

    $arquivo = UploadedFile::fake()->create('parecer.pdf', 100, 'application/pdf');

    $this->actingAs($admin)
        ->post(route('processos.store'), [
            'numero_sei' => '00000000000031',
            'arquivo' => $arquivo,
            'tipo' => 'pdf_sei',
        ])
        ->assertRedirect(route('processos.index'));

    $processo = Processo::query()->where('numero_sei', '00000000000031')->first();
    expect($processo)->not->toBeNull();

    $etapa = $processo->etapas()->orderBy('ordem')->first();
    expect($etapa)->not->toBeNull();
    expect($etapa->tipo)->toBe(Etapa::TIPO_JUIZO);
    expect($etapa->status)->toBe(Etapa::STATUS_EM_ELABORACAO);

    $documento = Documento::query()->where('etapa_id', $etapa->id)->first();
    expect($documento)->not->toBeNull();
    expect($documento->titulo)->toBe('parecer');
    expect($documento->tipo)->toBe('pdf_sei');
    expect($documento->upload_feito_por)->toBe('9999999');
    expect($documento->caminho)->toStartWith('uploads/');
    expect(file_exists(public_path($documento->caminho)))->toBeTrue();
});

it('nao deixa processo orfao quando o anexo falha', function () {
    $admin = adminDeCriacao();
    membroAtivoDeCriacao('1000001');

    $this->mock(\App\Services\DocumentoService::class, function ($mock) {
        $mock->shouldReceive('salvar')->once()->andThrow(new \RuntimeException('Falha simulada.'));
    })->makePartial();

    $this->actingAs($admin)
        ->post(route('processos.store'), [
            'numero_sei' => '00000000000032',
            'arquivo' => UploadedFile::fake()->create('parecer.pdf', 100, 'application/pdf'),
            'tipo' => 'pdf_sei',
        ])
        ->assertSessionHasErrors('arquivo')
        ->assertSessionHasInput('numero_sei');

    expect(Processo::query()->where('numero_sei', '00000000000032')->exists())->toBeFalse();
});