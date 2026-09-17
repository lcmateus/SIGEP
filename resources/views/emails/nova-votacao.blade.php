@component('emails.layout', ['titulo' => 'Nova votação aberta'])
    <p style="color:#334155;line-height:1.6;">Olá, <strong>{{ $nome }}</strong>!</p>

    <p style="color:#334155;line-height:1.6;">Uma nova votação foi aberta no SIGEP:</p>

    <table style="width:100%;border-collapse:collapse;margin:16px 0;">
        <tr>
            <td style="padding:8px 0;color:#475569;">Processo SEI</td>
            <td style="padding:8px 0;font-weight:bold;color:#064e3b;">{{ $numeroSei }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0;color:#475569;">Etapa</td>
            <td style="padding:8px 0;font-weight:bold;color:#064e3b;">{{ $etapaTipo }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0;color:#475569;">Encerramento</td>
            <td style="padding:8px 0;font-weight:bold;color:#064e3b;">{{ $encerramento }}</td>
        </tr>
    </table>

    <p style="color:#334155;line-height:1.6;">Acesse o sistema e registre seu voto antes do prazo.</p>
@endcomponent