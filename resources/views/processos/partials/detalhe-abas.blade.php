@php
    $userSiape = auth()->user()->siape;
    $isRelator = $processo->id_relator === $userSiape;
    $isAdmin = auth()->user() instanceof \App\Models\UsuarioAdministrador;
    $etapaAtual = $processo->etapa_atual;
    $etapaBloqueada = in_array($etapaAtual?->status, [
        \App\Models\Etapa::STATUS_DEVOLVIDO,
        \App\Models\Etapa::STATUS_ARQUIVADO,
    ]);
    $somenteLeitura = $somenteLeitura ?? false;
    $podeExcluir = !$somenteLeitura && ($isRelator || $isAdmin) && !$etapaBloqueada;
    $podeAnexar = !$somenteLeitura && ($isRelator || $isAdmin) && !$etapaBloqueada;
@endphp

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
                    <div id="docs-{{ $uid }}-{{ $etapaAtual->id }}">
                        @foreach($documentosPorEtapa->get($etapaAtual->id, collect()) as $documento)
                            <div class="flex items-center justify-between py-2 border-t border-slate-100 text-sm" data-doc-id="{{ $documento->id }}">
                                <div class="flex items-center gap-3">
                                    <span class="font-medium text-emerald-900">{{ $documento->titulo }}.{{ $documento->descricao }}</span>
                                    <span class="text-xs text-slate-400">{{ $documento->tipo_display }}</span>
                                    <span class="text-xs text-slate-400">{{ $documento->data_upload->format('d/m/Y H:i') }}</span>
                                </div>
                                <span class="flex items-center gap-2">
                                    <a href="{{ route('documentos.download', $documento) }}" title="Baixar"
                                        class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </a>
                                    @if($podeExcluir)
                                        <button type="button" onclick="excluirDocumento({{ $documento->id }}, this)" title="Excluir"
                                            class="text-red-500 hover:text-red-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>

                    @if($etapaAtual->status !== \App\Models\Etapa::STATUS_FINALIZADO && $podeAnexar)
                        <button type="button" onclick="abrirModalUpload({{ $etapaAtual->id }})"
                            class="mt-3 inline-flex items-center gap-1 text-xs font-bold text-emerald-700 hover:text-emerald-900">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Anexar documento
                        </button>
                    @endif
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
                    <div id="docs-{{ $uid }}-{{ $etapa->id }}">
                        @foreach($documentosPorEtapa->get($etapa->id, collect()) as $documento)
                            <div class="flex items-center justify-between py-2 border-t border-slate-100 text-sm" data-doc-id="{{ $documento->id }}">
                                <div class="flex items-center gap-3">
                                    <span class="font-medium text-emerald-900">{{ $documento->titulo }}.{{ $documento->descricao }}</span>
                                    <span class="text-xs text-slate-400">{{ $documento->tipo_display }}</span>
                                    <span class="text-xs text-slate-400">{{ $documento->data_upload->format('d/m/Y H:i') }}</span>
                                </div>
                                <span class="flex items-center gap-2">
                                    <a href="{{ route('documentos.download', $documento) }}" title="Baixar"
                                        class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </a>
                                    @if($podeExcluir)
                                        <button type="button" onclick="excluirDocumento({{ $documento->id }}, this)" title="Excluir"
                                            class="text-red-500 hover:text-red-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
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

<div id="modal-upload" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-emerald-900">Anexar Documento</h3>
            <button type="button" onclick="fecharModalUpload()" class="text-slate-400 hover:text-slate-700 text-xl font-bold leading-none">&times;</button>
        </div>

        <div id="upload-erro" class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700"></div>
        <div id="upload-sucesso" class="hidden mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700"></div>

        <form id="form-upload" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="etapa_id" id="upload-etapa-id">

            <div class="mb-4">
                <label class="block text-sm font-bold text-emerald-900 mb-1">Arquivo</label>
                <input type="file" name="arquivo" id="upload-arquivo" required
                    class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-bold text-emerald-900 mb-1">Tipo de documento</label>
                <select name="tipo" id="upload-tipo" required
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="pdf_sei">PDF do SEI</option>
                    <option value="relatorio_juizo">Relatorio do Juizo</option>
                    <option value="relatorio_pp">Relatorio do PP</option>
                    <option value="minuta_acpp">Minuta do ACPP</option>
                    <option value="versao_final_acpp">Versao Final do ACPP</option>
                    <option value="versao_assinada_acpp">Versao Assinada do ACPP</option>
                    <option value="diligencia">Diligencia</option>
                    <option value="prova_documental">Prova Documental</option>
                    <option value="prova_testemunhal">Prova Testemunhal</option>
                    <option value="prova_pericial">Prova Pericial</option>
                    <option value="relatorio_parcial_pae">Relatorio Parcial do PAE</option>
                    <option value="defesa_investigado">Defesa do Investigado</option>
                    <option value="alegacoes_finais">Alegacoes Finais</option>
                    <option value="reconsideracao">Reconsideracao</option>
                    <option value="outro" selected>Outro</option>
                </select>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="fecharModalUpload()"
                    class="bg-slate-200 hover:bg-slate-300 text-emerald-900 font-bold py-2 px-4 rounded-lg">
                    Cancelar
                </button>
                <button type="submit" id="btn-upload"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-lg">
                    Anexar
                </button>
            </div>
        </form>
    </div>
</div>

@once
<script>
    (function () {
        var csrfToken = '{{ csrf_token() }}';

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

        window.abrirModalUpload = function (etapaId) {
            document.getElementById('upload-etapa-id').value = etapaId;
            document.getElementById('upload-arquivo').value = '';
            document.getElementById('upload-tipo').value = 'outro';
            document.getElementById('upload-erro').classList.add('hidden');
            document.getElementById('upload-sucesso').classList.add('hidden');
            document.getElementById('modal-upload').classList.remove('hidden');
        };

        window.fecharModalUpload = function () {
            document.getElementById('modal-upload').classList.add('hidden');
        };

        document.getElementById('form-upload').addEventListener('submit', function (e) {
            e.preventDefault();

            var form = e.currentTarget;
            var btn = document.getElementById('btn-upload');
            var erroDiv = document.getElementById('upload-erro');
            var sucessoDiv = document.getElementById('upload-sucesso');

            erroDiv.classList.add('hidden');
            sucessoDiv.classList.add('hidden');

            var fd = new FormData(form);

            btn.disabled = true;
            btn.textContent = 'Enviando...';

            fetch('{{ route("documentos.store") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: fd
            })
            .then(function (r) {
                var contentType = r.headers.get('content-type') || '';
                if (contentType.indexOf('application/json') !== -1) {
                    return r.json().then(function (d) { return { ok: r.ok, data: d }; });
                }
                return r.text().then(function (t) {
                    console.error('Resposta nao-JSON (status ' + r.status + '):', t.substring(0, 500));
                    throw new Error('Servidor retornou status ' + r.status);
                });
            })
            .then(function (res) {
                if (!res.ok || !res.data.ok) {
                    var msg = res.data.erro || 'Erro ao enviar arquivo.';
                    if (res.data.errors) {
                        var keys = Object.keys(res.data.errors);
                        msg = res.data.errors[keys[0]][0];
                    }
                    erroDiv.textContent = msg;
                    erroDiv.classList.remove('hidden');
                    btn.disabled = false;
                    btn.textContent = 'Anexar';
                    return;
                }

                var doc = res.data.documento;
                var etapaId = fd.get('etapa_id');
                var container = document.getElementById('docs-{{ $uid }}-' + etapaId);

                if (!container) {
                    console.error('Container docs-{{ $uid }}-' + etapaId + ' nao encontrado');
                    location.reload();
                    return;
                }

                var semDocs = container.querySelector('.text-slate-500');
                if (semDocs) semDocs.remove();

                var div = document.createElement('div');
                div.className = 'flex items-center justify-between py-2 border-t border-slate-100 text-sm';
                div.setAttribute('data-doc-id', doc.id);

                var canDelete = {{ $podeExcluir ? 'true' : 'false' }};

                div.innerHTML =
                    '<div class="flex items-center gap-3">' +
                        '<span class="font-medium text-emerald-900">' + doc.titulo + '.' + doc.descricao + '</span>' +
                        '<span class="text-xs text-slate-400">' + doc.tipo_display + '</span>' +
                        '<span class="text-xs text-slate-400">' + doc.data_upload + '</span>' +
                    '</div>' +
                    '<span class="flex items-center gap-2">' +
                        '<a href="' + doc.download_url + '" title="Baixar" class="text-blue-600 hover:text-blue-800">' +
                            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>' +
                        '</a>' +
                        (canDelete ?
                            '<button type="button" onclick="excluirDocumento(' + doc.id + ', this)" title="Excluir" class="text-red-500 hover:text-red-700">' +
                                '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>' +
                            '</button>' : '') +
                    '</span>';

                container.appendChild(div);

                sucessoDiv.textContent = 'Documento anexado com sucesso.';
                sucessoDiv.classList.remove('hidden');

                form.reset();
                document.getElementById('upload-etapa-id').value = etapaId;

                btn.disabled = false;
                btn.textContent = 'Anexar';

                setTimeout(function () { sucessoDiv.classList.add('hidden'); }, 3000);
            })
            .catch(function (err) {
                console.error('Erro no upload:', err);
                erroDiv.textContent = 'Erro: ' + (err.message || 'conexao. Tente novamente.');
                erroDiv.classList.remove('hidden');
                btn.disabled = false;
                btn.textContent = 'Anexar';
            });
        });

        window.excluirDocumento = function (docId, btnEl) {
            if (!confirm('Tem certeza que deseja excluir este documento?')) return;

            fetch('{{ url("documentos") }}/' + docId, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
            })
            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
            .then(function (res) {
                if (!res.ok || !res.data.ok) {
                    alert(res.data.erro || 'Erro ao excluir documento.');
                    return;
                }

                var row = btnEl.closest('[data-doc-id]');
                if (row) row.remove();
            })
            .catch(function () {
                alert('Erro de conexao. Tente novamente.');
            });
        };
    })();
</script>
@endonce
