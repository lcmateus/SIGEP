@php
    $compacto = $compacto ?? false;
@endphp

<a href="{{ route('processos.documentos.download', $processo) }}"
    class="inline-block text-center bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-md transition-colors {{ $compacto ? 'text-xs px-3 py-1.5' : 'text-sm px-5 py-2' }}">
    Baixar documentos
</a>
<a href="{{ route('processos.votacoes.pdf', $processo) }}"
    class="inline-block text-center bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-md transition-colors {{ $compacto ? 'text-xs px-3 py-1.5' : 'text-sm px-5 py-2' }}">
    Baixar histórico (PDF)
</a>
