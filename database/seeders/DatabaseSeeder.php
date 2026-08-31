<?php

namespace Database\Seeders;

use App\Models\Documento;
use App\Models\Etapa;
use App\Models\Processo;
use App\Models\UsuarioAdministrador;
use App\Models\UsuarioMembro;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = UsuarioAdministrador::query()->firstOrCreate(
            ['siape' => '9999999'],
            [
                'nome' => 'Secretario Geral',
                'email' => 'secretario@sigep.test',
                'password' => bcrypt('password'),
            ]
        );

        $membros = collect();

        foreach (range(1, 4) as $i) {
            $siape = '100000' . $i;
            $membro = UsuarioMembro::query()->updateOrCreate(
                ['siape' => $siape],
                [
                    'nome' => "Membro Teste {$i}",
                    'email' => "membro{$i}@sigep.test",
                    'password' => bcrypt('password'),
                    'data_ativacao' => now(),
                    'ativado_por' => $admin->siape,
                    'is_presidente' => false,
                ]
            );
            $membros->push($membro);
        }

        $processos = collect();

        foreach (range(1, 4) as $i) {
            $processo = Processo::query()->updateOrCreate(
                ['numero_sei' => '000000000000' . $i],
                [
                    'data_admissao' => now()->subDays(30 - $i)->toDateString(),
                    'data_devolucao' => null,
                    'id_administrador' => $admin->siape,
                    'id_relator' => $membros->get($i - 1)->siape,
                ]
            );
            $processos->push($processo);
        }

        foreach (range(1, 4) as $i) {
            Etapa::query()->firstOrCreate(
                ['numero_sei_processo' => $processos->get($i - 1)->numero_sei, 'ordem' => 1],
                [
                    'tipo' => Etapa::TIPO_JUIZO,
                    'status' => Etapa::STATUS_EM_ELABORACAO,
                    'relatorio_texto' => 'Etapa inicial do processo de teste.',
                    'data_inicio' => now(),
                    'data_envio_votacao' => null,
                    'data_encerramento' => null,
                ]
            );
        }

        $arquivos = [
            1 => ['Fake 1', 'pdf', 'pdf_sei'],
            2 => ['Fake 2', 'pdf', 'pdf_sei'],
            3 => ['Fake 3', 'pdf', 'pdf_sei'],
            4 => ['Fake 4', 'pdf', 'pdf_sei'],
        ];

        foreach (range(1, 4) as $i) {
            $etapa = Etapa::query()
                ->where('numero_sei_processo', $processos->get($i - 1)->numero_sei)
                ->first();

            [$titulo, $extensao, $tipo] = $arquivos[$i];

            $caminho = 'uploads/' . $titulo . '.' . $extensao;
            if (!file_exists(public_path($caminho))) {
                continue;
            }

            Documento::query()->firstOrCreate(
                ['caminho' => $caminho],
                [
                    'titulo' => $titulo,
                    'descricao' => $extensao,
                    'upload_feito_por' => $membros->get($i - 1)->siape,
                    'data_upload' => now(),
                    'etapa_id' => $etapa->id,
                    'tipo' => $tipo,
                ]
            );
        }
    }
}
