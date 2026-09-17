@component('emails.layout', ['titulo' => 'Novo processo designado para você'])
    <p style="color:#334155;line-height:1.6;">Olá, <strong>{{ $nome }}</strong>!</p>

    <p style="color:#334155;line-height:1.6;">Você foi designado(a) como <strong>relator(a)</strong> de um novo processo no SIGEP:</p>

    <table style="width:100%;border-collapse:collapse;margin:16px 0;">
        <tr>
            <td style="padding:8px 0;color:#475569;">Processo SEI</td>
            <td style="padding:8px 0;font-weight:bold;color:#064e3b;">{{ $numeroSei }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0;color:#475569;">Etapa inicial</td>
            <td style="padding:8px 0;font-weight:bold;color:#064e3b;">{{ $etapaTipo }}</td>
        </tr>
    </table>

    <p style="color:#334155;line-height:1.6;">Acesse o sistema para consultar os detalhes do processo.</p>
@endcomponent