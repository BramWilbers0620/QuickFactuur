<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Betaling mislukt</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); padding: 30px; border-radius: 12px 12px 0 0; text-align: center;">
        <h1 style="color: white; margin: 0; font-size: 24px;">Betaling mislukt</h1>
    </div>

    <div style="background: #ffffff; padding: 30px; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 12px 12px;">
        <p style="margin-bottom: 20px;">Beste {{ $user->name }},</p>

        <p style="margin-bottom: 20px;">
            Helaas is de betaling voor je QuickFactuur abonnement niet gelukt.
            @if($amount)
            Het betreft een bedrag van <strong>€{{ number_format($amount / 100, 2, ',', '.') }}</strong>.
            @endif
            Dit kan verschillende oorzaken hebben:
        </p>

        <ul style="margin-bottom: 20px; padding-left: 20px;">
            <li>Onvoldoende saldo op je rekening</li>
            <li>Je kaart is verlopen</li>
            <li>De transactie is geblokkeerd door je bank</li>
        </ul>

        <p style="margin-bottom: 20px;">
            Om te voorkomen dat je toegang tot QuickFactuur wordt onderbroken, verzoeken wij je om je betaalgegevens bij te werken.
        </p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('billing') }}" style="display: inline-block; background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%); color: white; padding: 14px 28px; text-decoration: none; border-radius: 8px; font-weight: 600;">
                Betaalgegevens bijwerken
            </a>
        </div>

        <p style="margin-bottom: 20px; color: #6b7280; font-size: 14px;">
            Heb je vragen? Neem gerust contact met ons op door te reageren op deze email.
        </p>

        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">

        <p style="color: #9ca3af; font-size: 12px; text-align: center;">
            Met vriendelijke groet,<br>
            Het QuickFactuur Team
        </p>
    </div>
</body>
</html>
