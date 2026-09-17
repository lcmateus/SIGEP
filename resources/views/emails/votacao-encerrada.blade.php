@component('emails.layout', ['titulo' => 'Votação encerrada'])
    <p style="color:#334155;line-height:1.6;">Olá, <strong>{{ $nome }}</strong>!</p>

    <p style="color:#334155;line-height:1.6;">A votação do processo <strong>{{ $numeroSei }}</strong> foi <strong>encerrada</strong>.</p>

    <p style="color:#334155;line-height:1.6;">Resultado da rodada: <strong style="text-transform:uppercase;">{{ $resultado }}</strong></p>

    <p style="color:#334155;line-height:1.6;">Acesse o sistema para mais detalhes.</p>
@endcomponent