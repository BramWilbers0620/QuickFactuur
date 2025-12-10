<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abonnement gestart</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #374151;
            margin: 0;
            padding: 20px;
            background-color: #f9fafb;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 32px 24px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .content {
            padding: 32px 24px;
        }
        .highlight {
            background-color: #ecfdf5;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 16px;
            margin: 24px 0;
        }
        .button {
            display: inline-block;
            background-color: #10b981;
            color: white;
            padding: 12px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            margin: 16px 0;
        }
        .footer {
            background-color: #f3f4f6;
            padding: 24px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        ul li {
            margin: 8px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Je abonnement is actief!</h1>
        </div>

        <div class="content">
            <p>Hallo {{ $user->name }},</p>

            <p>Bedankt dat je een abonnement bij QuickFactuur bent gestart! Je betaling is succesvol verwerkt.</p>

            <div class="highlight">
                <strong>Je abonnement: {{ $planName }}</strong><br>
                <small>Je kunt nu onbeperkt professionele facturen genereren.</small>
            </div>

            <p>Met je abonnement krijg je toegang tot:</p>
            <ul>
                <li>✅ Onbeperkt facturen aanmaken</li>
                <li>✅ Professionele PDF-facturen</li>
                <li>✅ Automatische nummerering</li>
                <li>✅ BTW-berekening</li>
                <li>✅ Stripe factuuroverzicht</li>
                <li>✅ Volledige toegang tot alle functies</li>
            </ul>

            <p>Begin direct met factureren:</p>
            <a href="{{ url('/dashboard') }}" class="button">Ga naar dashboard</a>

            <p>Je kunt je abonnement altijd beheren via je dashboard. Bij vragen kun je contact opnemen via <a href="mailto:support@quickfactuur.nl">support@quickfactuur.nl</a></p>

            <p>Veel succes met je facturatie!</p>
            <p><strong>Het QuickFactuur team</strong></p>
        </div>

        <div class="footer">
            <p>QuickFactuur - Professioneel factureren, simpel gemaakt</p>
            <p><a href="{{ url('/abonnement') }}" style="color: #10b981;">Abonnement beheren</a> | <a href="{{ url('/facturen') }}" style="color: #10b981;">Mijn facturen</a></p>
        </div>
    </div>
</body>
</html>
