@extends('layouts.main_layout', [
    'titulo' => 'Visualizar documento',
])

@section('content')
    <main class="mx-auto max-w-5xl rounded-xl border border-slate-200 bg-white p-6 shadow-sm sm:p-10">
        <header class="mb-8 flex flex-wrap items-center justify-between gap-4 border-b border-slate-200 pb-5">
            <div>
                <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">Visualizacao do documento</p>
                <h1 class="mt-1 break-words text-xl font-bold text-slate-900">{{ $documento->nome_original }}</h1>
                <p class="mt-1 text-sm text-slate-500">{{ $documento->tipo_display }}</p>
            </div>
            <a href="{{ route('documentos.download', $documento) }}"
                class="inline-flex items-center rounded-lg bg-emerald-700 px-4 py-2 text-sm font-bold text-white hover:bg-emerald-800">
                Baixar documento
            </a>
        </header>

        <article class="docx-conteudo space-y-4 break-words text-[15px] leading-7 text-slate-800">
            @forelse($blocos as $bloco)
                @if($bloco['tipo'] === 'paragrafo')
                    <p class="whitespace-pre-wrap">{{ $bloco['texto'] }}</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full border-collapse border border-slate-300 text-sm">
                            <tbody>
                                @foreach($bloco['linhas'] as $linha)
                                    <tr>
                                        @foreach($linha as $celula)
                                            <td class="whitespace-pre-wrap border border-slate-300 px-3 py-2 align-top">{{ $celula }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @empty
                <p class="text-slate-500">Este documento nao possui texto para exibir.</p>
            @endforelse
        </article>
    </main>
@endsection
