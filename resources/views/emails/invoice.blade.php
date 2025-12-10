<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factuur {{ $invoice->invoice_number }}</title>
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
            background: linear-gradient(135deg, #2563eb, #4f46e5);
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
        .invoice-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #e2e8f0;
        }
        .invoice-details table {
            width: 100%;
        }
        .invoice-details td {
            padding: 8px 0;
        }
        .invoice-details .label {
            color: #64748b;
            font-size: 14px;
        }
        .invoice-details .value {
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
        .button {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 15px;
        }
        .note {
            margin-top: 20px;
            padding: 15px;
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            border-radius: 4px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Factuur {{ $invoice->invoice_number }}</h1>
        <p style="margin: 10px 0 0 0; opacity: 0.9;">Van {{ $invoice->company_name }}</p>
    </div>

    <div class="content">
        <p>Beste {{ $invoice->customer_name }},</p>

        <p>Hierbij ontvangt u factuur <strong>{{ $invoice->invoice_number }}</strong> voor onze diensten/producten.</p>

        <div class="invoice-details">
            <table>
                <tr>
                    <td class="label">Factuurnummer</td>
                    <td class="value">{{ $invoice->invoice_number }}</td>
                </tr>
                <tr>
                    <td class="label">Factuurdatum</td>
                    <td class="value">{{ $invoice->invoice_date->format('d-m-Y') }}</td>
                </tr>
                @if($invoice->due_date)
                <tr>
                    <td class="label">Vervaldatum</td>
                    <td class="value">{{ $invoice->due_date->format('d-m-Y') }}</td>
                </tr>
                @endif
                <tr>
                    <td class="label">Subtotaal</td>
                    <td class="value">€{{ number_format($invoice->amount, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">BTW</td>
                    <td class="value">€{{ number_format($invoice->vat_amount, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="2" style="border-top: 1px solid #e2e8f0; padding-top: 15px;">
                        <table width="100%">
                            <tr>
                                <td class="label" style="font-size: 16px;">Totaal te betalen</td>
                                <td class="value total">€{{ number_format($invoice->total, 2, ',', '.') }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        @if($invoice->company_iban)
        <div class="note">
            <strong>Betalingsgegevens:</strong><br>
            IBAN: {{ $invoice->company_iban }}<br>
            T.n.v.: {{ $invoice->company_name }}<br>
            O.v.v.: {{ $invoice->invoice_number }}
        </div>
        @endif

        <p style="margin-top: 25px;">De factuur vindt u als bijlage bij deze e-mail.</p>

        <p>Met vriendelijke groet,<br>
        <strong>{{ $invoice->company_name }}</strong></p>
    </div>

    <div class="footer">
        <p>
            {{ $invoice->company_name }}
            @if($invoice->company_email) • {{ $invoice->company_email }} @endif
            @if($invoice->company_phone) • {{ $invoice->company_phone }} @endif
        </p>
        <p style="margin-top: 10px; font-size: 11px;">
            Deze e-mail is automatisch gegenereerd door QuickFactuur.
        </p>
    </div>
</body>
</html>
