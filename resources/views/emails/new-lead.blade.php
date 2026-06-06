<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Novo Lead — Salão Fácil</title>
    <style>
        body { margin: 0; padding: 0; background: #f4f5fb; font-family: 'Segoe UI', Arial, sans-serif; }
        .wrapper { max-width: 560px; margin: 40px auto; }
        .header {
            background: linear-gradient(135deg, #6C63FF, #9b59b6);
            border-radius: 16px 16px 0 0;
            padding: 32px 36px;
            text-align: center;
        }
        .header h1 { color: #fff; font-size: 1.4rem; margin: 0; font-weight: 700; }
        .header p  { color: rgba(255,255,255,.8); margin: 8px 0 0; font-size: .9rem; }
        .body {
            background: #fff;
            padding: 36px;
            border-radius: 0 0 16px 16px;
            box-shadow: 0 8px 32px rgba(108,99,255,.08);
        }
        .alert {
            background: #f0efff;
            border-left: 4px solid #6C63FF;
            padding: 14px 18px;
            border-radius: 0 8px 8px 0;
            margin-bottom: 28px;
            font-size: .9rem;
            color: #4a4a68;
        }
        .field { margin-bottom: 20px; }
        .field label {
            display: block;
            font-size: .75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #aaa;
            margin-bottom: 4px;
        }
        .field .value {
            font-size: 1rem;
            font-weight: 600;
            color: #1a1a2e;
        }
        .whatsapp-btn {
            display: block;
            background: #25D366;
            color: #fff;
            text-align: center;
            padding: 14px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 700;
            font-size: .95rem;
            margin-top: 28px;
        }
        .footer {
            text-align: center;
            padding: 24px;
            color: #bbb;
            font-size: .78rem;
        }
        .divider { border: none; border-top: 1px solid #f0f0f0; margin: 20px 0; }
    </style>
</head>
<body>
<div class="wrapper">

    <div class="header">
        <h1>🎉 Novo interesse recebido!</h1>
        <p>Alguém preencheu o formulário da landing page</p>
    </div>

    <div class="body">

        <div class="alert">
            Entre em contato o quanto antes — leads respondem melhor nas primeiras horas!
        </div>

        <div class="field">
            <label>Nome</label>
            <div class="value">{{ $lead->name }}</div>
        </div>

        <hr class="divider">

        <div class="field">
            <label>WhatsApp</label>
            <div class="value">{{ $lead->whatsapp }}</div>
        </div>

        <hr class="divider">

        <div class="field">
            <label>Tipo de negócio</label>
            <div class="value">{{ $lead->business_type }}</div>
        </div>

        <hr class="divider">

        <div class="field">
            <label>Recebido em</label>
            <div class="value">{{ $lead->created_at->format('d/m/Y \à\s H:i') }}</div>
        </div>

        <a href="https://wa.me/55{{ preg_replace('/\D/', '', $lead->whatsapp) }}"
           class="whatsapp-btn">
            💬 Abrir conversa no WhatsApp
        </a>

    </div>

    <div class="footer">
        Salão Fácil &mdash; Sistema de Agendamento Online<br>
        Este e-mail foi gerado automaticamente.
    </div>

</div>
</body>
</html>
