<?php

use App\Models\Documento;
use App\Models\Etapa;
use App\Models\Processo;
use App\Models\UsuarioAdministrador;
use App\Models\UsuarioMembro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

function adminParaListagens(): UsuarioAdministrador
{
    return UsuarioAdministrador::query()->create([
        'siape' => '9999999',
        'nome' => 'Secretario',
        'email' => 'secretario@sigep.test',
        'password' => bcrypt('SenhaForte#1234'),
    ]);
}

function membroParaListagens(): UsuarioMembro
{
    return UsuarioMembro::query()->create([
        'siape' => '1000001',
        'nome' => 'Relator',
        'email' => 'relator@sigep.test',
        'password' => bcrypt('SenhaForte#1234'),
        'data_ativacao' => now(),
        'ativado_por' => '9999999',
    ]);
}

function processoParaListagens(): Processo
{
    membroParaListagens();

    return Processo::query()->create([
        'numero_sei' => '0000000000'.random_int(1000, 9999),
        'data_admissao' => now()->toDateString(),
        'id_administrador' => '9999999',
        'id_relator' => '1000001',
    ]);
}

function etapaParaListagens(Processo $processo, string $status): Etapa
{
    return Etapa::query()->create([
        'numero_sei_processo' => $processo->numero_sei,
        'ordem' => 1,
        'tipo' => Etapa::TIPO_JUIZO,
        'status' => $status,
    ]);
}

function documentoParaListagens(Etapa $etapa): string
{
    $diretorio = public_path('uploads');

    if (! is_dir($diretorio)) {
        mkdir($diretorio, 0755, true);
    }

    $nomeSalvo = 'listagem-'.Str::uuid().'.txt';
    $caminho = $diretorio.'/'.$nomeSalvo;

    file_put_contents($caminho, 'Conteúdo do documento');

    Documento::query()->create([
        'titulo' => 'parecer',
        'descricao' => 'txt',
        'caminho' => 'uploads/'.$nomeSalvo,
        'upload_feito_por' => '9999999',
        'data_upload' => now(),
        'etapa_id' => $etapa->id,
        'tipo' => 'relatorio_juizo',
    ]);

    return $caminho;
}

afterEach(function () {
    $diretorio = public_path('uploads');

    if (is_dir($diretorio)) {
        foreach (glob($diretorio.'/listagem-*.txt') as $arquivo) {
            unlink($arquivo);
        }
    }
});

it('exibe os downloads no modal de em andamento', function () {
    $admin = adminParaListagens();
    $processo = processoParaListagens();
    etapaParaListagens($processo, Etapa::STATUS_EM_VOTACAO);

    $response = $this->actingAs($admin)
        ->get(route('processos.publicos'));

    $response->assertOk();

    $this->assertMatchesRegularExpression(
        '/\/processos\/[A-Za-z0-9_-]+\/documentos\/download/',
        $response->getContent()
    );
    $this->assertMatchesRegularExpression(
        '/\/processos\/[A-Za-z0-9_-]+\/votacoes\/pdf/',
        $response->getContent()
    );
});

it('gera o link de detalhes dos arquivados com a origem correta', function () {
    $admin = adminParaListagens();
    $processo = processoParaListagens();
    etapaParaListagens($processo, Etapa::STATUS_ARQUIVADO);

    $response = $this->actingAs($admin)
        ->get(route('processos.arquivados'));

    $response->assertOk();

    $this->assertMatchesRegularExpression(
        '/\/processos\/[A-Za-z0-9_-]+\?origem=arquivados/',
        $response->getContent()
    );
});

it('mostra os downloads e volta para arquivados na pagina de detalhes', function () {
    $admin = adminParaListagens();
    $processo = processoParaListagens();
    etapaParaListagens($processo, Etapa::STATUS_ARQUIVADO);

    $response = $this->actingAs($admin)
        ->get(route('processos.show', ['processo' => $processo, 'origem' => 'arquivados']));

    $response->assertOk();

    $this->assertMatchesRegularExpression(
        '/\/processos\/[A-Za-z0-9_-]+\/documentos\/download/',
        $response->getContent()
    );
    $this->assertMatchesRegularExpression(
        '/\/processos\/[A-Za-z0-9_-]+\/votacoes\/pdf/',
        $response->getContent()
    );
    $this->assertMatchesRegularExpression(
        '/<a href="'.preg_quote(route('processos.arquivados'), '/').'"[^>]*>\s*Voltar\s*<\/a>/',
        $response->getContent()
    );
});

it('mantem o zip e o pdf funcionando para processos arquivados', function () {
    $admin = adminParaListagens();
    $processo = processoParaListagens();
    $etapa = etapaParaListagens($processo, Etapa::STATUS_ARQUIVADO);
    documentoParaListagens($etapa);

    $zip = $this->actingAs($admin)
        ->get(route('processos.documentos.download', $processo));

    $zip->assertOk();

    $caminhoZip = $zip->getFile()->getPathname();

    if (is_file($caminhoZip)) {
        unlink($caminhoZip);
    }

    $pdf = $this->actingAs($admin)
        ->get(route('processos.votacoes.pdf', $processo));

    $pdf->assertOk();
    expect($pdf->headers->get('Content-Type'))->toContain('application/pdf');
    expect(substr($pdf->getContent(), 0, 4))->toBe('%PDF');
});
