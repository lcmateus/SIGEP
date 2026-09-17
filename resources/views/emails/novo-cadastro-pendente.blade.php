@component('emails.layout', ['titulo' => 'Novo cadastro de membro aguardando aprovação'])
    <p style="color:#334155;line-height:1.6;">Um novo usuário se cadastrou no sistema e aguarda aprovação:</p>

    <table style="width:100%;border-collapse:collapse;margin:16px 0;">
        <tr>
            <td style="padding:8px 0;color:#475569;">Nome</td>
            <td style="padding:8px 0;font-weight:bold;color:#064e3b;">{{ $nome }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0;color:#475569;">SIAPE</td>
            <td style="padding:8px 0;font-weight:bold;color:#064e3b;">{{ $siape }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0;color:#475569;">E-mail</td>
            <td style="padding:8px 0;font-weight:bold;color:#064e3b;">{{ $email }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0;color:#475569;">Cadastrado em</td>
            <td style="padding:8px 0;font-weight:bold;color:#064e3b;">{{ $data }}</td>
        </tr>
    </table>
@endcomponent