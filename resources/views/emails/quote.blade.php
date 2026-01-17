<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offerte {{ $quote->quote_number }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #059669, #10b981);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            background: #f8fafc;
            padding: 30px;
            border: 1px solid #e2e8f0;
        }
        .quote-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #e2e8f0;
        }
        .quote-details table {
            width: 100%;
        }
        .quote-details td {
            padding: 8px 0;
        }
        .quote-details .label {
            color: #64748b;
            font-size: 14px;
        }
        .quote-details .value {
            font-weight: bold;
            text-align: right;
        }
        .total {
            font-size: 24px;
            color: #059669;
            font-weight: bold;
        }
        .footer {
            background: #f1f5f9;
            padding: 20px 30px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-radius: 0 0 10px 10px;
            border: 1px solid #e2e8f0;
            border-top: none;
        }
        .note {
            margin-top: 20px;
            padding: 15px;
            background: #ecfdf5;
            border-left: 4px solid #10b981;
            border-radius: 4px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Offerte {{ $quote->quote_number }}</h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">Van {{ $quote->company_name }}</p>
    </div>

    <div class="content">
        <p>Beste {{ $quote->customer_name }},</p>

        <p>Hierbij ontvangt u offerte <strong>{{ $quote->quote_number }}</strong> voor onze diensten/producten.</p>

        <div class="quote-details">
            <table>
                <tr>
                    <td class="label">Offertenummer</td>
                    <td class="value">{{ $quote->quote_number }}</td>
                </tr>
                <tr>
                    <td class="label">Offertedatum</td>
                    <td class="value">{{ $quote->quote_date->format('d-m-Y') }}</td>
                </tr>
                @if($quote->valid_until)
                <tr>
                    <td class="label">Geldig tot</td>
                    <td class="value">{{ $quote->valid_until->format('d-m-Y') }}</td>
                </tr>
                @endif
                <tr>
                    <td class="label">Subtotaal</td>
                    <td class="value">€{{ number_format($quote->amount, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">BTW</td>
                    <td class="value">€{{ number_format($quote->vat_amount, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="2" style="border-top: 1px solid #e2e8f0; padding-top: 15px;">
                        <table width="100%">
                            <tr>
                                <td class="label" style="font-size: 16px;">Totaal</td>
                                <td class="value total">€{{ number_format($quote->total, 2, ',', '.') }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        @if($quote->valid_until)
        <div class="note">
            <strong>Let op:</strong> Deze offerte is geldig tot <strong>{{ $quote->valid_until->format('d-m-Y') }}</strong>.
            Neem contact met ons op als u vragen heeft of de offerte wilt accepteren.
        </div>
        @endif

        <p style="margin-top: 25px;">De offerte vindt u als bijlage bij deze e-mail.</p>

        <p>Met vriendelijke groet,<br>
        <strong>{{ $quote->company_name }}</strong></p>
    </div>

    <div class="footer">
        <p>
            {{ $quote->company_name }}
            @if($quote->company_email) • {{ $quote->company_email }} @endif
            @if($quote->company_phone) • {{ $quote->company_phone }} @endif
        </p>
        <p style="margin-top: 10px; font-size: 11px;">
            Deze e-mail is automatisch gegenereerd door QuickFactuur.
        </p>
    </div>
</body>
</html>
