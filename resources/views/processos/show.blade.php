@extends('layouts.main_layout', [
    'titulo' => 'Processo',
])

@section('content')
    <div class="max-w-4xl">
        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6 mb-6">
            <div class="flex items-start justify-between gap-6">
                <div>
                    <h2 class="font-bold text-emerald-900 text-2xl">Processo SEI {{ $processo->numero_sei }}</h2>
                    <p class="text-sm text-slate-600 mt-2">
                        <span class="font-bold text-emerald-900">Relator:</span>
                        {{ $processo->relator?->nome ?? 'Nao designado' }}
                        ·
                        <span class="font-bold text-emerald-900">Secretário(a):</span>
                        {{ $processo->administrador?->nome ?? 'Nao informado' }}
                    </p>
                    <p class="text-sm text-slate-500 mt-1">
                        Admissao: {{ $processo->data_admissao?->format('d/m/Y') ?? 'Nao informado' }}
                        @if($processo->data_devolucao)
                            · Devolucao: {{ $processo->data_devolucao->format('d/m/Y') }}
                        @endif
                    </p>
                </div>

                @if($processo->etapa_atual)
                    <span class="bg-emerald-600 px-5 py-1 rounded-md text-sm font-semibold tracking-wide text-white">
                        {{ $processo->etapa_atual->tipo_display }}
                    </span>
                @else
                    <span class="bg-slate-400 px-5 py-1 rounded-md text-sm font-semibold tracking-wide text-white">
                        Sem etapa
                    </span>
                @endif
            </div>

            <div class="mt-6 flex gap-3">
                <a href="{{ route('processos.index') }}" class="inline-block text-center bg-slate-200 hover:bg-slate-300 text-emerald-900 text-sm font-bold px-5 py-2 rounded-md">
                    Voltar
                </a>
            </div>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
            @include('processos.partials.detalhe-abas', [
                'uid' => 'show',
                'processo' => $processo,
                'documentosPorEtapa' => $documentosPorEtapa,
                'rodadas' => $rodadas,
            ])

            @php
                $etapaAtual = $processo->etapa_atual;
                $isAdmin = auth()->user() instanceof \App\Models\UsuarioAdministrador;
                $mostrarProximoPasso = $etapaAtual
                    && $etapaAtual->tipo !== \App\Models\Etapa::TIPO_JUIZO
                    && $etapaAtual->status !== \App\Models\Etapa::STATUS_ARQUIVADO
                    && $etapaAtual->status !== \App\Models\Etapa::STATUS_DEVOLVIDO;
            @endphp

            @if($mostrarProximoPasso)
                <div class="mt-6 pt-6 border-t border-slate-200">
                    <h3 class="font-bold text-emerald-900 text-lg mb-4">Qual o proximo passo?</h3>
                    <div class="flex flex-wrap gap-3">
                        <form action="{{ route('processos.proximo-passo', $processo) }}" method="POST"
                            onsubmit="return confirm('Tem certeza que deseja devolver este processo ao Secretario Geral?')">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="decisao" value="devolver">
                            <button type="submit"
                                class="bg-amber-500 hover:bg-amber-600 text-white text-sm font-bold px-4 py-2 rounded-lg">
                                Devolver ao Secretario Geral
                            </button>
                        </form>

                        <form action="{{ route('processos.proximo-passo', $processo) }}" method="POST"
                            onsubmit="return confirm('Prosseguir para Processo de Apuracao (PAE)?')">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="decisao" value="pae">
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-4 py-2 rounded-lg">
                                Prosseguir para PAE
                            </button>
                        </form>

                        <form action="{{ route('processos.proximo-passo', $processo) }}" method="POST"
                            onsubmit="return confirm('Prosseguir para Acordo de Conduta (ACPP)?')">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="decisao" value="acpp">
                            <button type="submit"
                                class="bg-purple-600 hover:bg-purple-700 text-white text-sm font-bold px-4 py-2 rounded-lg">
                                ACPP
                            </button>
                        </form>

                        <button type="button" disabled
                            class="bg-slate-300 text-slate-500 text-sm font-bold px-4 py-2 rounded-lg cursor-not-allowed"
                            title="Em breve">
                            Iniciar Votacao
                        </button>
                    </div>
                </div>
            @endif

            @php
                $mostrarAcoesAdmin = $isAdmin
                    && $etapaAtual
                    && $etapaAtual->status === \App\Models\Etapa::STATUS_DEVOLVIDO;
            @endphp

            @if($mostrarAcoesAdmin)
                <div class="mt-6 pt-6 border-t border-slate-200">
                    <h3 class="font-bold text-emerald-900 text-lg mb-4">Acoes do Administrador</h3>
                    <div class="flex flex-wrap gap-3">
                        <form action="{{ route('processos.admin-acao', $processo) }}" method="POST"
                            onsubmit="return confirm('Devolver este processo ao relator? O status voltara para Em Elaboracao.')">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="decisao" value="reativar">
                            <button type="submit"
                                class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-4 py-2 rounded-lg">
                                Devolver Ao Relator
                            </button>
                        </form>

                        <form action="{{ route('processos.admin-acao', $processo) }}" method="POST"
                            onsubmit="return confirm('Arquivar este processo?')">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="decisao" value="arquivar">
                            <button type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white text-sm font-bold px-4 py-2 rounded-lg">
                                Arquivar
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
