<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $mensagem->assunto }}</title>
</head>
<body style="margin:0;background:#f3f7fb;color:#10265f;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f3f7fb;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:620px;background:#ffffff;border:1px solid #dce8f4;border-radius:14px;overflow:hidden;">
                    <tr>
                        <td style="background:#0c2f91;padding:24px 32px;color:#ffffff;font-size:20px;font-weight:700;">
                            Universidade Aquafast
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:34px 32px;">
                            <p style="margin:0 0 10px;color:#1676dc;font-size:13px;font-weight:700;letter-spacing:.8px;text-transform:uppercase;">{{ $mensagem->titulo }}</p>
                            <h1 style="margin:0 0 22px;color:#10265f;font-size:25px;line-height:1.25;">{{ $mensagem->saudacao }}</h1>

                            @foreach ($mensagem->linhas as $linha)
                                <p style="margin:0 0 14px;color:#4c5f7d;font-size:16px;line-height:1.6;">{{ $linha }}</p>
                            @endforeach

                            @if ($mensagem->acaoTexto && $mensagem->acaoUrl)
                                <table role="presentation" cellspacing="0" cellpadding="0" style="margin-top:26px;">
                                    <tr>
                                        <td style="border-radius:7px;background:#1266d8;">
                                            <a href="{{ $mensagem->acaoUrl }}" style="display:inline-block;padding:14px 22px;color:#ffffff;font-size:15px;font-weight:700;text-decoration:none;">{{ $mensagem->acaoTexto }}</a>
                                        </td>
                                    </tr>
                                </table>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="border-top:1px solid #e7eef6;padding:20px 32px;color:#77859a;font-size:12px;line-height:1.5;">
                            Esta é uma mensagem automática da Universidade Aquafast.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
