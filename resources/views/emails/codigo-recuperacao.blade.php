@component('emails.layout', ['titulo' => 'Codigo de recuperacao de senha'])
    <p style="color:#334155;line-height:1.6;">Olá, <strong>{{ $nome }}</strong>!</p>

    <p style="color:#334155;line-height:1.6;">Recebemos uma solicitacao de recuperacao de senha para sua conta no SIGEP.</p>

    <p style="color:#334155;line-height:1.6;">Utilize o codigo abaixo para redefinir sua senha. Ele e valido ate <strong>{{ $expiraEm }}</strong>:</p>

    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:16px;text-align:center;margin:16px 0;letter-spacing:6px;font-size:28px;font-weight:bold;color:#047857;">
        {{ $codigo }}
    </div>

    <p style="color:#334155;line-height:1.6;">Se voce nao solicitou a recuperacao, ignore este e-mail.</p>
@endcomponent