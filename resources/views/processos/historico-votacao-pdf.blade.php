<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Histórico de votações SIGEP</title>

    @include('pdf.partials.estilos')

    <style>
        .info {
            margin-bottom: 14px;
        }

        .info td {
            border: 0;
            padding: 2px 0;
            width: 50%;
        }

        .rodada {
            margin-bottom: 18px;
            page-break-inside: avoid;
        }

        .rodada-title {
            color: #065f46;
            font-size: 11px;
            font-weight: bold;
            margin: 0 0 3px;
        }

        .rodada-meta {
            color: #64748b;
            font-size: 9px;
            margin: 0 0 7px;
        }

        .badge {
            border-radius: 4px;
            color: #ffffff;
            display: inline-block;
            font-size: 8px;
            font-weight: bold;
            padding: 3px 7px;
        }

        .badge-aprovado {
            background: #059669;
        }

        .badge-reprovado {
            background: #dc2626;
        }

        .badge-minerva {
            background: #d97706;
        }

        .badge-pendente {
            background: #64748b;
        }

        .minerva {
            background: #fffbeb;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">SIGEP · Sistema de Gestão</div>
        <h1>Histórico de votações</h1>
        <p class="subtitle">Processo SEI {{ $processo->numero_sei }}</p>
    </div>

    <table class="info">
        <tr>
            <td><strong>Relator:</strong> {{ $processo->relator?->nome ?? 'Não designado' }}</td>
            <td><strong>Secretário(a):</strong> {{ $processo->administrador?->nome ?? 'Não informado' }}</td>
        </tr>
        <tr>
            <td><strong>Admissão:</strong> {{ $processo->data_admissao?->format('d/m/Y') ?? 'Não informado' }}</td>
            <td><strong>Devolução:</strong> {{ $processo->data_devolucao?->format('d/m/Y') ?? '-' }}</td>
        </tr>
    </table>

    <table class="cards">
        <tr>
            <td class="card">
                <div class="card-label">Rodadas</div>
                <div class="card-value">{{ $rodadas->count() }}</div>
            </td>
            <td class="card">
                <div class="card-label">Aprovadas</div>
                <div class="card-value">{{ $rodadas->where('resultado', 'aprovado')->count() }}</div>
            </td>
            <td class="card">
                <div class="card-label">Reprovadas</div>
                <div class="card-value">{{ $rodadas->where('resultado', 'reprovado')->count() }}</div>
            </td>
            <td class="card">
                <div class="card-label">Sem resultado</div>
                <div class="card-value">{{ $rodadas->whereNull('resultado')->count() }}</div>
            </td>
        </tr>
    </table>

    @if($rodadas->isEmpty())
        <p class="empty">Este processo não possui votações registradas.</p>
    @else
        @foreach($processo->etapas->sortBy('ordem') as $etapa)
            <h2>Etapa {{ $etapa->ordem }} · {{ $etapa->tipo_display }} ({{ $etapa->status_display }})</h2>

            @forelse($rodadas->where('id_etapa', $etapa->id)->sortBy('data_abertura') as $rodada)
                @php
                    $resultado = $rodada->resultado;
                    $aguardaMinerva = $rodada->etapa?->isAguardandoMinerva();

                    $badge = match (true) {
                        $resultado === 'aprovado' => ['badge-aprovado', 'Aprovado'],
                        $resultado === 'reprovado' => ['badge-reprovado', 'Reprovado'],
                        $aguardaMinerva => ['badge-minerva', 'Aguardando Minerva'],
                        default => ['badge-pendente', 'Pendente'],
                    };
                @endphp

                <div class="rodada">
                    <p class="rodada-title">
                        Votação #{{ $rodada->id }}
                        <span class="badge {{ $badge[0] }}">{{ $badge[1] }}</span>
                    </p>
                    <p class="rodada-meta">
                        Abertura: {{ $rodada->data_abertura?->format('d/m/Y H:i') ?? '-' }}
                        &nbsp;·&nbsp;
                        Encerramento: {{ $rodada->data_encerramento?->format('d/m/Y H:i') ?? '-' }}
                        &nbsp;·&nbsp;
                        Presidente: {{ $rodada->presidente?->nome ?? 'Não informado' }}
                    </p>

                    <table class="data">
                        <thead>
                            <tr>
                                <th>Membro</th>
                                <th>Voto</th>
                                <th>Observação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rodada->votos as $voto)
                                @php
                                    $opcao = match ($voto->opcao) {
                                        'aprova' => 'Aprovou',
                                        'aprova com resalva' => 'Aprovou com ressalva',
                                        'desaprova' => 'Reprovou',
                                        'desaprova com resalva' => 'Reprovou com ressalva',
                                        'abstenho' => 'Abstenção',
                                        default => $voto->opcao,
                                    };
                                @endphp
                                <tr class="{{ $voto->is_minerva ? 'minerva' : '' }}">
                                    <td>{{ $voto->membro?->nome ?? 'Membro excluído (anonimizado)' }}</td>
                                    <td>{{ $opcao }}</td>
                                    <td>
                                        @if($voto->is_minerva)
                                            <span class="badge badge-minerva">Minerva</span>
                                        @endif
                                        {{ $voto->justificativa }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="empty">Nenhum voto registrado nesta rodada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @empty
                <p class="empty">Nenhuma votação nesta etapa.</p>
            @endforelse
        @endforeach
    @endif

    <div class="footer">
        SIGEP · Histórico gerado em {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
