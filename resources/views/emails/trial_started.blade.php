<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gratis proefperiode gestart</title>
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
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
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
            background-color: #eff6ff;
            border: 1px solid #dbeafe;
            border-radius: 6px;
            padding: 16px;
            margin: 24px 0;
        }
        .button {
            display: inline-block;
            background-color: #3b82f6;
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
            <h1>🎉 Je gratis proefperiode is gestart!</h1>
        </div>

        <div class="content">
            <p>Hallo {{ $user->name }},</p>

            <p>Welkom bij QuickFactuur! Je gratis proefperiode van 7 dagen is nu actief gestart.</p>

            <div class="highlight">
                <strong>Je proefperiode loopt tot {{ $trialEndsAt }}</strong><br>
                <small>Na deze datum kun je kiezen voor een maandelijks (€5) of jaarlijks abonnement (€50).</small>
            </div>

            <p>Tijdens je proefperiode kun je:</p>
            <ul>
                <li>✅ Onbeperkt facturen aanmaken</li>
                <li>✅ Professionele PDF-facturen genereren</li>
                <li>✅ Al je bedrijfsgegevens instellen</li>
                <li>✅ Alle functionaliteit van QuickFactuur gebruiken</li>
            </ul>

            <p>Ga direct aan de slag met je eerste factuur:</p>
            <a href="{{ url('/dashboard') }}" class="button">Start met factureren</a>

            <p>Heb je vragen? Neem gerust contact met ons op via <a href="mailto:support@quickfactuur.nl">support@quickfactuur.nl</a></p>

            <p>Veel succes met je facturatie!</p>
            <p><strong>Het QuickFactuur team</strong></p>
        </div>

        <div class="footer">
            <p>QuickFactuur - Professioneel factureren, simpel gemaakt</p>
            <p><a href="{{ url('/abonnement') }}" style="color: #3b82f6;">Abonnement beheren</a> | <a href="{{ url('/') }}" style="color: #3b82f6;">Website</a></p>
        </div>
    </div>
</body>
</html>
