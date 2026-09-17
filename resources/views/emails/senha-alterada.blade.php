@component('emails.layout', ['titulo' => 'Senha alterada'])
    <p style="color:#334155;line-height:1.6;">Olá, <strong>{{ $nome }}</strong>!</p>

    <p style="color:#334155;line-height:1.6;">A senha da sua conta no SIGEP foi <strong>alterada com sucesso</strong> em {{ $data }}.</p>
@endcomponent