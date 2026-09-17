<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;">
    <div style="max-width:600px;margin:24px auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
        <div style="background:#047857;padding:20px 28px;">
            <h1 style="margin:0;color:#ffffff;font-size:20px;">SIGEP</h1>
        </div>
        <div style="padding:28px;">
            <h2 style="margin:0 0 16px;color:#064e3b;font-size:18px;">{{ $titulo }}</h2>
            {{ $slot }}
        </div>
        <div style="background:#f8fafc;padding:16px 28px;color:#64748b;font-size:12px;line-height:1.6;">
            <p style="margin:0;font-weight:bold;color:#b91c1c;">Não responda este e-mail.</p>
            <p style="margin:4px 0 0;">Esta é uma mensagem automática do Sistema de Gestão da Ética Pública (SIGEP). Esta caixa de e-mail não é monitorada.</p>
        </div>
    </div>
</body>
</html>