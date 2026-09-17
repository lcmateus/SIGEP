@component('emails.layout', ['titulo' => 'Cadastro aprovado'])
    <p style="color:#334155;line-height:1.6;">Olá, <strong>{{ $nome }}</strong>!</p>

    <p style="color:#334155;line-height:1.6;">Seu cadastro no SIGEP foi <strong>aprovado</strong>. Você já pode acessar o sistema e participar das votações.</p>

    <p style="color:#334155;line-height:1.6;">Acesse o sistema para começar a usar sua conta.</p>
@endcomponent