<?php

use App\Models\Documento;
use App\Models\Etapa;
use App\Models\Processo;
use App\Models\RodadaVotacao;
use App\Models\UsuarioAdministrador;
use App\Models\UsuarioMembro;
use App\Models\Voto;
use Illuminate\Support\Str;

function adminDeDownloads(): UsuarioAdministrador
{
    return UsuarioAdministrador::query()->create([
        'siape' => '9999999',
        'nome' => 'Secretário',
        'email' => 'secretario@sigep.test',
        'password' => bcrypt('SenhaForte#1234'),
    ]);
}

function membroDeDownloads(string $siape): UsuarioMembro
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

function processoDeDownloads(): Processo
{
    return Processo::query()->create([
        'numero_sei' => '00000000000040',
        'data_admissao' => now()->toDateString(),
        'id_administrador' => '9999999',
        'id_relator' => '1000001',
    ]);
}

function etapaDeDownloads(Processo $processo, int $ordem): Etapa
{
    return Etapa::query()->create([
        'numero_sei_processo' => $processo->numero_sei,
        'ordem' => $ordem,
        'tipo' => Etapa::TIPO_JUIZO,
        'status' => Etapa::STATUS_FINALIZADO,
    ]);
}

function documentoDeDownloads(Etapa $etapa, string $titulo, string $tipo): Documento
{
    $diretorio = public_path('uploads');

    if (! is_dir($diretorio)) {
        mkdir($diretorio, 0755, true);
    }

    $nomeSalvo = Str::uuid().'.txt';
    file_put_contents($diretorio.'/'.$nomeSalvo, "Conteudo de {$titulo}");

    return Documento::query()->create([
        'titulo' => $titulo,
        'descricao' => 'txt',
        'caminho' => 'uploads/'.$nomeSalvo,
        'upload_feito_por' => '9999999',
        'data_upload' => now(),
        'etapa_id' => $etapa->id,
        'tipo' => $tipo,
    ]);
}

afterEach(function () {
    foreach (Documento::query()->pluck('caminho') as $caminho) {
        $arquivo = public_path($caminho);
        if (is_file($arquivo)) {
            unlink($arquivo);
        }
    }
});

it('baixa um zip com todos os documentos exceto o pdf do sei', function () {
    $admin = adminDeDownloads();
    membroDeDownloads('1000001');
    $processo = processoDeDownloads();
    $etapa = etapaDeDownloads($processo, 1);

    documentoDeDownloads($etapa, 'parecer', 'relatorio_juizo');
    documentoDeDownloads($etapa, ' diligencia ', 'diligencia');
    documentoDeDownloads($etapa, 'sei', Documento::TIPO_PDF_SEI);

    $response = $this->actingAs($admin)
        ->get(route('processos.documentos.download', $processo));

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toContain('zip');

    $caminhoZip = $response->getFile()->getPathname();
    $zip = new ZipArchive;

    expect($zip->open($caminhoZip))->toBeTrue();

    $nomes = [];
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $nomes[] = $zip->getNameIndex($i);
    }

    $zip->close();
    unlink($caminhoZip);

    expect($nomes)->toHaveCount(2);
    expect(collect($nomes)->some(fn ($nome) => str_contains($nome, 'parecer')))->toBeTrue();
    expect(collect($nomes)->some(fn ($nome) => str_contains($nome, 'diligencia')))->toBeTrue();
    expect(collect($nomes)->some(fn ($nome) => str_contains($nome, 'sei')))->toBeFalse();
});

it('nao gera zip quando so existe o pdf do sei', function () {
    $admin = adminDeDownloads();
    membroDeDownloads('1000001');
    $processo = processoDeDownloads();
    $etapa = etapaDeDownloads($processo, 1);

    documentoDeDownloads($etapa, 'sei', Documento::TIPO_PDF_SEI);

    $url = route('processos.show', $processo);

    $this->actingAs($admin)
        ->from($url)
        ->get(route('processos.documentos.download', $processo))
        ->assertRedirect($url)
        ->assertSessionHas('error');
});

it('gera o pdf do historico completo de votacoes do processo', function () {
    $admin = adminDeDownloads();
    $presidente = membroDeDownloads('1000001');
    $membro = membroDeDownloads('1000002');
    $processo = processoDeDownloads();
    $etapa = etapaDeDownloads($processo, 1);

    $rodada = RodadaVotacao::query()->create([
        'data_abertura' => now()->subDay(),
        'data_encerramento' => now(),
        'resultado' => 'aprovado',
        'id_etapa' => $etapa->id,
        'id_presidente' => $presidente->siape,
    ]);

    Voto::query()->create([
        'opcao' => 'aprova com resalva',
        'justificativa' => 'ressalva registrada',
        'is_minerva' => true,
        'id_membro' => $membro->siape,
        'id_rodada' => $rodada->id,
    ]);

    $response = $this->actingAs($admin)
        ->get(route('processos.votacoes.pdf', $processo));

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toContain('application/pdf');
    expect(substr($response->getContent(), 0, 4))->toBe('%PDF');
});

it('gera o pdf mesmo quando o processo nao tem votacoes', function () {
    $admin = adminDeDownloads();
    membroDeDownloads('1000001');
    $processo = processoDeDownloads();

    $response = $this->actingAs($admin)
        ->get(route('processos.votacoes.pdf', $processo));

    $response->assertOk();
    expect(substr($response->getContent(), 0, 4))->toBe('%PDF');
});

it('exige autenticacao para baixar documentos e historico', function (string $rota) {
    $admin = adminDeDownloads();
    membroDeDownloads('1000001');
    $processo = processoDeDownloads();

    $this->get(route($rota, $processo))
        ->assertRedirect(route('login'));
})->with([
    'processos.documentos.download',
    'processos.votacoes.pdf',
]);
