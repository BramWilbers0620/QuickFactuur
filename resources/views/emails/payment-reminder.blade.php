<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Betalingsherinnering</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0; font-size: 24px;">Betalingsherinnering</h1>
    </div>

    <div style="background: #f8f9fa; padding: 30px; border: 1px solid #e9ecef; border-top: none;">
        <p>Beste {{ $invoice->customer_name }},</p>

        <p>
            Wij willen u vriendelijk herinneren aan de openstaande factuur <strong>{{ $invoice->invoice_number }}</strong>.
        </p>

        <div style="background: white; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; margin: 20px 0;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; color: #6c757d;">Factuurnummer:</td>
                    <td style="padding: 8px 0; text-align: right; font-weight: bold;">{{ $invoice->invoice_number }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #6c757d;">Factuurdatum:</td>
                    <td style="padding: 8px 0; text-align: right;">{{ $invoice->invoice_date->format('d-m-Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #6c757d;">Vervaldatum:</td>
                    <td style="padding: 8px 0; text-align: right; color: #dc3545;">{{ $invoice->due_date->format('d-m-Y') }}</td>
                </tr>
                <tr style="border-top: 2px solid #dee2e6;">
                    <td style="padding: 12px 0; color: #6c757d; font-weight: bold;">Openstaand bedrag:</td>
                    <td style="padding: 12px 0; text-align: right; font-weight: bold; font-size: 18px; color: #dc3545;">{{ $invoice->formatted_total }}</td>
                </tr>
            </table>
        </div>

        @if($daysOverdue > 0)
        <p style="color: #dc3545;">
            <strong>Let op:</strong> Deze factuur is {{ $daysOverdue }} {{ $daysOverdue === 1 ? 'dag' : 'dagen' }} over de vervaldatum.
        </p>
        @endif

        <p>
            Mocht u de betaling inmiddels hebben voldaan, dan kunt u deze herinnering als niet verzonden beschouwen.
        </p>

        <p>
            Heeft u vragen over deze factuur? Neem dan gerust contact met ons op.
        </p>

        <p style="margin-top: 30px;">
            Met vriendelijke groet,<br>
            <strong>{{ $invoice->company_name }}</strong>
        </p>

        @if($invoice->company_phone || $invoice->company_email)
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #dee2e6; font-size: 14px; color: #6c757d;">
            @if($invoice->company_phone)
                Tel: {{ $invoice->company_phone }}<br>
            @endif
            @if($invoice->company_email)
                E-mail: {{ $invoice->company_email }}
            @endif
        </div>
        @endif
    </div>

    <div style="background: #343a40; color: #adb5bd; padding: 20px; border-radius: 0 0 10px 10px; text-align: center; font-size: 12px;">
        <p style="margin: 0;">Deze e-mail is verzonden via QuickFactuur</p>
    </div>
</body>
</html>
