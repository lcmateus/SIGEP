@component('emails.layout', ['titulo' => 'Processo devolvido novamente a você'])
    <p style="color:#334155;line-height:1.6;">Olá, <strong>{{ $nome }}</strong>!</p>

    <p style="color:#334155;line-height:1.6;">O processo <strong>{{ $numeroSei }}</strong> foi devolvido a você pela Secretaria e voltou para <strong>Em Elaboração</strong>.</p>

    <p style="color:#334155;line-height:1.6;">Acesse o sistema para prosseguir com o processo.</p>
@endcomponent