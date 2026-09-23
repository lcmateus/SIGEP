@component('emails.layout', ['titulo' => 'Novo presidente eleito'])
    <p style="color:#334155;line-height:1.6;">Olá, <strong>{{ $nome }}</strong>!</p>

    <p style="color:#334155;line-height:1.6;">Você foi <strong>designado(a) presidente</strong> de votação(ões) em andamento no SIGEP.</p>

    <p style="color:#334155;line-height:1.6;">Como presidente da rodada, você será responsável pelo <strong>voto de Minerva</strong> quando houver empate nas votações.</p>

    <p style="color:#334155;line-height:1.6;">Acesse o sistema para saber mais.</p>
@endcomponent