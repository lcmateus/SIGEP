<?php

use App\Models\Documento;
use App\Models\Etapa;
use App\Models\Processo;
use App\Models\RodadaVotacao;
use App\Models\UsuarioAdministrador;
use App\Models\UsuarioMembro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

function administradorDeVisualizacao(): UsuarioAdministrador
{
    return UsuarioAdministrador::query()->create([
        'siape' => '9900001',
        'nome' => 'Secretario de teste',
        'email' => 'secretario-visualizacao@sigep.test',
        'password' => bcrypt('SenhaForte#1234'),
    ]);
}

function documentoDeVisualizacao(string $extensao, string $nomeArquivo, string $conteudo): Documento
{
    $diretorio = public_path('uploads');
    if (! is_dir($diretorio)) {
        mkdir($diretorio, 0755, true);
    }

    $nomeSalvo = 'preview-'.Str::uuid().'.'.$extensao;
    file_put_contents($diretorio.'/'.$nomeSalvo, $conteudo);

    $admin = UsuarioAdministrador::query()->firstOrCreate(
        ['siape' => '9900001'],
        [
            'nome' => 'Secretario de teste',
            'email' => 'secretario-visualizacao@sigep.test',
            'password' => bcrypt('SenhaForte#1234'),
        ],
    );
    $membro = UsuarioMembro::query()->create([
        'siape' => '9900002',
        'nome' => 'Membro de teste',
        'email' => 'membro-visualizacao@sigep.test',
        'password' => bcrypt('SenhaForte#1234'),
        'data_ativacao' => now(),
        'ativado_por' => $admin->siape,
    ]);
    $processo = Processo::query()->create([
        'numero_sei' => '00000000990001',
        'data_admissao' => now()->toDateString(),
        'id_administrador' => $admin->siape,
        'id_relator' => $membro->siape,
    ]);
    $etapa = Etapa::query()->create([
        'numero_sei_processo' => $processo->numero_sei,
        'ordem' => 1,
        'tipo' => Etapa::TIPO_JUIZO,
        'status' => Etapa::STATUS_EM_ELABORACAO,
    ]);

    return Documento::query()->create([
        'titulo' => pathinfo($nomeArquivo, PATHINFO_FILENAME),
        'descricao' => $extensao,
        'caminho' => 'uploads/'.$nomeSalvo,
        'upload_feito_por' => $admin->siape,
        'data_upload' => now(),
        'etapa_id' => $etapa->id,
        'tipo' => 'outro',
    ]);
}

afterEach(function () {
    foreach (glob(public_path('uploads/preview-*')) ?: [] as $arquivo) {
        if (is_file($arquivo)) {
            unlink($arquivo);
        }
    }
});

it('abre PDFs no navegador com disposicao inline', function () {
    $admin = administradorDeVisualizacao();
    $documento = documentoDeVisualizacao('pdf', 'parecer.pdf', "%PDF-1.4\npreview");

    $this->actingAs($admin)
        ->get(route('documentos.visualizar', $documento))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/pdf')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('Content-Disposition', 'inline; filename=parecer.pdf');
});

it('exibe o texto de arquivos DOCX em uma pagina do SIGEP', function () {
    $admin = administradorDeVisualizacao();
    $caminho = tempnam(sys_get_temp_dir(), 'sigep-docx-');
    $zip = new ZipArchive;
    expect($zip->open($caminho, ZipArchive::OVERWRITE))->toBeTrue();
    $zip->addFromString('word/document.xml', '<?xml version="1.0" encoding="UTF-8"?><w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:body><w:p><w:r><w:t>Texto de pre-visualizacao</w:t></w:r></w:p></w:body></w:document>');
    $zip->close();
    $conteudo = file_get_contents($caminho);
    unlink($caminho);

    $documento = documentoDeVisualizacao('docx', 'relatorio.docx', $conteudo);

    $this->actingAs($admin)
        ->get(route('documentos.visualizar', $documento))
        ->assertOk()
        ->assertSee('Texto de pre-visualizacao')
        ->assertSee('Baixar documento');
});

it('exibe a acao de visualizar na tela de detalhes do processo', function () {
    $admin = administradorDeVisualizacao();
    $documento = documentoDeVisualizacao('txt', 'nota.txt', 'texto');

    $this->actingAs($admin)
        ->get(route('processos.show', $documento->etapa->processo))
        ->assertOk()
        ->assertSee(route('documentos.visualizar', $documento), false);
});

it('exige autenticacao para visualizar documentos', function () {
    $admin = administradorDeVisualizacao();
    $documento = documentoDeVisualizacao('txt', 'nota.txt', 'texto');

    auth()->logout();

    $this->get(route('documentos.visualizar', $documento))
        ->assertRedirect(route('login'));
});

it('atribui o presidente atual as rodadas abertas antigas sem presidente', function () {
    $documento = documentoDeVisualizacao('txt', 'nota.txt', 'texto');
    $presidente = UsuarioMembro::query()->findOrFail('9900002');
    $presidente->update(['is_presidente' => true]);
    $rodada = RodadaVotacao::query()->create([
        'data_abertura' => now(),
        'data_encerramento' => now()->addWeek(),
        'resultado' => null,
        'id_etapa' => $documento->etapa_id,
        'id_presidente' => null,
    ]);

    $migracao = require database_path('migrations/2026_11_22_000000_assign_current_president_to_open_rounds.php');
    $migracao->up();

    expect($rodada->fresh()->id_presidente)->toBe($presidente->siape);
});
