@component('emails.layout', ['titulo' => 'Votação encerra amanhã'])
    <p style="color:#334155;line-height:1.6;">Olá, <strong>{{ $nome }}</strong>!</p>

    <p style="color:#334155;line-height:1.6;">A votação do processo <strong>{{ $numeroSei }}</strong> encerra <strong>amanhã</strong>.</p>

    <p style="color:#334155;line-height:1.6;"><strong>Prazo:</strong> {{ $encerramento }}</p>

    <p style="color:#334155;line-height:1.6;">Caso ainda não tenha votado, acesse o sistema e registre seu voto para não perder o prazo.</p>
@endcomponent