@php
    $resultado = $rodada->resultado;
    $aguardaMinerva = $rodada->etapa?->isAguardandoMinerva();
    $badge = match (true) {
        $resultado === 'aprovado' => ['bg-emerald-600', 'Aprovado'],
        $resultado === 'reprovado' => ['bg-red-600', 'Reprovado'],
        $aguardaMinerva => ['bg-amber-500', 'Aguardando Minerva'],
        default => ['bg-slate-500', 'Pendente'],
    };
@endphp

<div class="py-3 border-t border-slate-100 first:border-t-0 text-sm">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="font-bold text-emerald-900">Votação #{{ $rodada->id }}</p>
            <p class="text-xs text-slate-500 mt-1">
                Abertura: {{ $rodada->data_abertura?->format('d/m/Y H:i') ?? '-' }}
                · Encerramento: {{ $rodada->data_encerramento?->format('d/m/Y H:i') ?? '-' }}
                · Presidente da Rodada: {{ $rodada->presidente?->nome ?? 'Nao informado' }}
            </p>
        </div>
        <span class="shrink-0 px-3 py-1 rounded-md text-xs font-semibold tracking-wide text-white {{ $badge[0] }}">
            {{ $badge[1] }}
        </span>
    </div>

    <table class="mt-3 w-full text-left">
        <thead>
            <tr class="text-xs text-slate-500">
                <th class="py-1 pr-3 font-bold">Membro</th>
                <th class="py-1 pr-3 font-bold">Voto</th>
                <th class="py-1 font-bold">Observação</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rodada->votos as $voto)
                @php
                    $opcaoDisplay = match ($voto->opcao) {
                        'aprova' => 'Aprovou',
                        'aprova com resalva' => 'Aprovou com ressalva',
                        'desaprova' => 'Reprovou',
                        'abstenho' => 'Abstenção',
                        default => $voto->opcao,
                    };
                @endphp
                <tr class="{{ $voto->is_minerva ? 'bg-amber-50' : '' }} border-t border-slate-100">
                    <td class="py-1.5 pr-3 text-emerald-900">
                        {{ $voto->membro?->nome ?? 'Membro excluído (anonimizado)' }}
                    </td>
                    <td class="py-1.5 pr-3">{{ $opcaoDisplay }}</td>
                    <td class="py-1.5">
                        @if($voto->is_minerva)
                            <span class="text-xs font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-md">Minerva</span>
                        @endif
                        @if($voto->justificativa)
                            <span class="text-xs text-slate-500">{{ $voto->justificativa }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="py-1.5 text-slate-500 text-xs">Nenhum voto registrado nesta rodada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>