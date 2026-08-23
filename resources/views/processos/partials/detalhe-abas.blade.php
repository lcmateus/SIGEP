<div data-tabs-container="{{ $uid }}">
    <div class="flex gap-1 border-b border-slate-200 mb-4">
        <button type="button" data-tab-btn="{{ $uid }}" data-tab="documentos"
            class="px-4 py-2 text-sm font-bold rounded-t-lg bg-emerald-600 text-white">
            Documentos
        </button>
        <button type="button" data-tab-btn="{{ $uid }}" data-tab="votacoes"
            class="px-4 py-2 text-sm font-bold rounded-t-lg text-slate-500 hover:text-emerald-700">
            Historico de Votacoes
        </button>
    </div>

    <div data-tab-panel="{{ $uid }}" data-tab="documentos">
        @php
            $etapaAtual = $processo->etapa_atual;
            $outrasEtapas = $processo->etapas
                ->sortByDesc('ordem')
                ->reject(fn ($etapa) => $etapaAtual && $etapa->id === $etapaAtual->id);
        @endphp

        @if(!$processo->etapas->count())
            <p class="text-sm text-slate-500 py-3">Este processo nao possui etapas registradas.</p>
        @endif

        @if($etapaAtual)
            <details open class="mb-3 border border-slate-200 rounded-lg bg-white">
                <summary class="cursor-pointer px-4 py-3 text-sm font-bold text-emerald-900 hover:bg-slate-50 rounded-lg">
                    Etapa atual: {{ $etapaAtual->tipo_display }}
                    <span class="text-slate-400 font-medium">({{ $etapaAtual->status_display }})</span>
                </summary>
                <div class="px-4 pb-3">
                    @forelse($documentosPorEtapa->get($etapaAtual->id, collect()) as $documento)
                        <div class="flex items-center justify-between py-2 border-t border-slate-100 text-sm">
                            <span>{{ $documento->titulo }}</span>
                            <span class="flex items-center gap-3">
                                <span class="text-xs text-slate-400">{{ $documento->tipo_display }}</span>
                                <a href="{{ $documento->url }}" target="_blank" rel="noopener"
                                    class="text-blue-700 font-bold">Abrir</a>
                            </span>
                        </div>
                    @empty
                        <p class="py-2 border-t border-slate-100 text-sm text-slate-500">Nenhum documento nesta etapa.</p>
                    @endforelse
                </div>
            </details>
        @endif

        @foreach($outrasEtapas as $etapa)
            <details class="mb-3 border border-slate-200 rounded-lg bg-white">
                <summary class="cursor-pointer px-4 py-3 text-sm font-bold text-slate-600 hover:bg-slate-50 rounded-lg">
                    {{ $etapa->tipo_display }}
                    <span class="text-slate-400 font-medium">({{ $etapa->status_display }})</span>
                </summary>
                <div class="px-4 pb-3">
                    @forelse($documentosPorEtapa->get($etapa->id, collect()) as $documento)
                        <div class="flex items-center justify-between py-2 border-t border-slate-100 text-sm">
                            <span>{{ $documento->titulo }}</span>
                            <span class="flex items-center gap-3">
                                <span class="text-xs text-slate-400">{{ $documento->tipo_display }}</span>
                                <a href="{{ $documento->url }}" target="_blank" rel="noopener"
                                    class="text-blue-700 font-bold">Abrir</a>
                            </span>
                        </div>
                    @empty
                        <p class="py-2 border-t border-slate-100 text-sm text-slate-500">Nenhum documento nesta etapa.</p>
                    @endforelse
                </div>
            </details>
        @endforeach
    </div>

    <div data-tab-panel="{{ $uid }}" data-tab="votacoes" class="hidden">
        @forelse($rodadas as $rodada)
            <div class="flex items-center justify-between py-3 border-b border-slate-100 text-sm">
                <div>
                    <p class="font-bold text-emerald-900">
                        Votacao #{{ $rodada->id }}
                        <span class="font-medium text-slate-400">
                            · {{ $rodada->etapa?->tipo_display ?? 'Sem etapa' }}
                        </span>
                    </p>
                    <p class="text-xs text-slate-500 mt-1">
                        Abertura: {{ $rodada->data_abertura?->format('d/m/Y H:i') ?? '-' }}
                        · Encerramento: {{ $rodada->data_encerramento?->format('d/m/Y H:i') ?? '-' }}
                    </p>
                </div>
                <div class="text-right">
                    <span class="inline-block px-3 py-1 rounded-md text-xs font-semibold text-white {{ ['aberta' => 'bg-green-600', 'encerrada' => 'bg-slate-500', 'cancelada' => 'bg-red-500'][$rodada->status] ?? 'bg-slate-400' }}">
                        {{ ucfirst($rodada->status) }}
                    </span>
                    <p class="text-xs text-slate-500 mt-1">
                        Resultado: {{ $rodada->resultado ? str_replace('_', ' ', ucfirst($rodada->resultado)) : 'Pendente' }}
                    </p>
                </div>
            </div>
        @empty
            <p class="text-sm text-slate-500 py-3">Este processo nao possui votacoes registradas.</p>
        @endforelse
    </div>
</div>

@once
    <script>
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-tab-btn]');
            if (!btn) return;

            var uid = btn.getAttribute('data-tab-btn');
            var nome = btn.getAttribute('data-tab');

            document.querySelectorAll('[data-tabs-container="' + uid + '"] [data-tab-btn]').forEach(function (b) {
                if (b === btn) {
                    b.classList.add('bg-emerald-600', 'text-white');
                    b.classList.remove('text-slate-500');
                } else {
                    b.classList.remove('bg-emerald-600', 'text-white');
                    b.classList.add('text-slate-500');
                }
            });

            document.querySelectorAll('[data-tabs-container="' + uid + '"] [data-tab-panel]').forEach(function (painel) {
                painel.classList.toggle('hidden', painel.getAttribute('data-tab') !== nome);
            });
        });

        document.addEventListener('click', function (e) {
            var abrir = e.target.closest('[data-abrir-detalhe]');
            if (abrir) {
                var alvo = document.querySelector(abrir.getAttribute('data-abrir-detalhe'));
                if (alvo) alvo.classList.remove('hidden');
                return;
            }

            var fechar = e.target.closest('[data-fechar-detalhe]');
            if (fechar) {
                fechar.closest('.detalhe-overlay').classList.add('hidden');
            }
        });
    </script>
@endonce
