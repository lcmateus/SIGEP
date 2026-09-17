@component('emails.layout', ['titulo' => 'Processo devolvido à Secretaria'])
    <p style="color:#334155;line-height:1.6;">Olá, <strong>{{ $nome }}</strong>!</p>

    <p style="color:#334155;line-height:1.6;">O processo abaixo foi <strong>devolvido</strong> à Secretaria pelo relator:</p>

    <table style="width:100%;border-collapse:collapse;margin:16px 0;">
        <tr>
            <td style="padding:8px 0;color:#475569;">Processo SEI</td>
            <td style="padding:8px 0;font-weight:bold;color:#064e3b;">{{ $numeroSei }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0;color:#475569;">Relator</td>
            <td style="padding:8px 0;font-weight:bold;color:#064e3b;">{{ $relatorNome }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0;color:#475569;">Devolvido em</td>
            <td style="padding:8px 0;font-weight:bold;color:#064e3b;">{{ $data }}</td>
        </tr>
    </table>

    <p style="color:#334155;line-height:1.6;">Acesse o sistema para reativar ou arquivar o processo.</p>
@endcomponent