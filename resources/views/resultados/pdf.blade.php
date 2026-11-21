<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de resultados SIGEP</title>

    @include('pdf.partials.estilos')
</head>
<body>
    <div class="header">
        <div class="brand">SIGEP · Sistema de Gestão</div>
        <h1>Relatório de resultados</h1>
        <p class="subtitle">Indicadores consolidados do sistema</p>
    </div>

    <div class="period">
        <strong>Período selecionado</strong>
        {{ $periodoLabel ?? 'Últimos 30 dias' }}
        <span class="muted">
            ({{ $inicio->format('d/m/Y') }} a {{ $fim->format('d/m/Y') }})
        </span>
    </div>

    <table class="cards">
        <tr>
            <td class="card">
                <div class="card-label">Processos recebidos</div>
                <div class="card-value">{{ $totalProcessos }}</div>
            </td>
            <td class="card">
                <div class="card-label">Em andamento</div>
                <div class="card-value">{{ $processosEmAndamento }}</div>
            </td>
            <td class="card">
                <div class="card-label">Concluídos</div>
                <div class="card-value">{{ $processosFinalizados }}</div>
            </td>
            <td class="card">
                <div class="card-label">Taxa de conclusão</div>
                <div class="card-value">
                    {{ number_format($taxaConclusao, 1, ',', '.') }}%
                </div>
            </td>
        </tr>
    </table>

    <h2>Processos por situação</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Situação</th>
                <th class="value">Quantidade</th>
            </tr>
        </thead>
        <tbody>
            @forelse($processosPorStatus as $status => $quantidade)
                <tr>
                    <td>{{ $status }}</td>
                    <td class="value">{{ $quantidade }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="empty">
                        Não há processos no período selecionado.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Processos por tipo da etapa atual</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Tipo</th>
                <th class="value">Quantidade</th>
            </tr>
        </thead>
        <tbody>
            @forelse($processosPorTipo as $tipo => $quantidade)
                <tr>
                    <td>{{ $tipo }}</td>
                    <td class="value">{{ $quantidade }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="empty">
                        Não há etapas no período selecionado.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Votações</h2>
    <table class="data">
        <tbody>
            <tr>
                <td>Total de votações</td>
                <td class="value">{{ $totalVotacoes }}</td>
            </tr>
            <tr>
                <td>Votações abertas</td>
                <td class="value">{{ $votacoesAbertas }}</td>
            </tr>
            <tr>
                <td>Votações encerradas</td>
                <td class="value">{{ $votacoesEncerradas }}</td>
            </tr>
            <tr>
                <td>Aprovadas</td>
                <td class="value">{{ $votacoesAprovadas }}</td>
            </tr>
            <tr>
                <td>Reprovadas</td>
                <td class="value">{{ $votacoesReprovadas }}</td>
            </tr>
        </tbody>
    </table>

    <h2>Votos registrados</h2>
    <table class="data">
        <tbody>
            <tr>
                <td>Total de votos</td>
                <td class="value">{{ $totalVotos }}</td>
            </tr>
            <tr>
                <td>Aprova</td>
                <td class="value">{{ $votosAprova }}</td>
            </tr>
            <tr>
                <td>Desaprova</td>
                <td class="value">{{ $votosDesaprova }}</td>
            </tr>
            <tr>
                <td>Aprova com ressalva</td>
                <td class="value">{{ $votosRessalva }}</td>
            </tr>
            <tr>
                <td>Abstenções</td>
                <td class="value">{{ $votosAbstencao }}</td>
            </tr>
        </tbody>
    </table>

    <h2>Usuários</h2>
    <table class="data">
        <tbody>
            <tr>
                <td>Membros cadastrados</td>
                <td class="value">{{ $totalMembros }}</td>
            </tr>
            <tr>
                <td>Membros ativos</td>
                <td class="value">{{ $membrosAtivos }}</td>
            </tr>
            <tr>
                <td>Novos membros no período</td>
                <td class="value">{{ $novosMembros }}</td>
            </tr>
            <tr>
                <td>Administradores</td>
                <td class="value">{{ $totalAdministradores }}</td>
            </tr>
        </tbody>
    </table>

    <h2>Relatores com mais processos no período</h2>
    <table class="data">
        <thead>
            <tr>
                <th>Relator</th>
                <th class="value">Processos</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rankingRelatores as $relator)
                <tr>
                    <td>{{ $relator->nome }}</td>
                    <td class="value">{{ $relator->processos_count }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="empty">
                        Não há dados de relatores no período.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        SIGEP · Relatório gerado em {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>