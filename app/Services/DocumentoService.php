<?php

namespace App\Services;

use App\Models\Documento;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use RuntimeException;

class DocumentoService
{
    /**
     * Salva o arquivo em public/uploads e cria o registro de Documento.
     *
     * @throws RuntimeException quando o arquivo e invalido ou nao pode ser movido.
     */
    public function salvar(UploadedFile $arquivo, int $etapaId, string $tipo, string $siape): Documento
    {
        if (!$arquivo->isValid()) {
            throw new RuntimeException('Arquivo invalido.');
        }

        $nomeOriginal = $arquivo->getClientOriginalName();
        $titulo = pathinfo($nomeOriginal, PATHINFO_FILENAME);
        $extensao = pathinfo($nomeOriginal, PATHINFO_EXTENSION);
        $nomeSalvo = Str::uuid() . '.' . $extensao;

        $diretorio = public_path('uploads');

        if (!is_dir($diretorio)) {
            mkdir($diretorio, 0755, true);
        }

        $arquivo->move($diretorio, $nomeSalvo);

        $caminhoCompleto = public_path('uploads/' . $nomeSalvo);

        if (!file_exists($caminhoCompleto)) {
            throw new RuntimeException('Nao foi possivel salvar o arquivo.');
        }

        $caminho = 'uploads/' . $nomeSalvo;

        return Documento::query()->create([
            'titulo' => $titulo,
            'descricao' => $extensao,
            'caminho' => $caminho,
            'upload_feito_por' => $siape,
            'etapa_id' => $etapaId,
            'tipo' => $tipo,
            'data_upload' => now(),
        ]);
    }
}