<!DOCTYPE html>
<html lang="nl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Factuur {{ $invoice_number }}</title>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            line-height: 1.4;
            margin: 0;
            padding: 30px 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .brand-color { color: {{ $brand_color }}; }
        .gray { color: #6b7280; }
        .small { font-size: 9px; }

        .mt-1 { margin-top: 5px; }
        .mt-2 { margin-top: 10px; }
        .mt-3 { margin-top: 15px; }
        .mt-4 { margin-top: 20px; }
        .mt-6 { margin-top: 30px; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .p-2 { padding: 8px 10px; }
        .p-3 { padding: 12px 15px; }

        .header-title {
            font-size: 28px;
            font-weight: bold;
            color: {{ $brand_color }};
        }

        .invoice-label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
        }

        .section-title {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 3px;
        }

        .contact-info {
            color: #4b5563;
            font-size: 10px;
            line-height: 1.5;
        }

        .logo {
            max-height: 60px;
            max-width: 180px;
        }

        .items-table {
            margin-top: 25px;
            border: 1px solid #e5e7eb;
        }

        .items-table thead {
            background: #f8fafc;
        }

        .items-table th {
            font-size: 9px;
            font-weight: 600;
            color: {{ $brand_color }};
            text-transform: uppercase;
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
        }

        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: top;
        }

        .items-table tr:last-child td {
            border-bottom: none;
        }

        .totals-table {
            width: 250px;
            margin-left: auto;
            margin-top: 20px;
        }

        .totals-table td {
            padding: 6px 0;
        }

        .totals-table .total-row {
            border-top: 2px solid {{ $brand_color }};
            font-size: 14px;
        }

        .totals-table .total-row td {
            padding-top: 10px;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }

        .payment-info {
            background: #f8fafc;
            padding: 15px;
            border-left: 3px solid {{ $brand_color }};
            margin-top: 25px;
        }

        .notes-section {
            margin-top: 25px;
            padding: 15px;
            background: #fefce8;
            border-left: 3px solid #eab308;
            font-size: 10px;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <table>
        <tr>
            <td style="width: 50%;">
                @if($logo_data)
                    <img src="{{ $logo_data }}" class="logo" alt="Logo">
                @else
                    <div class="header-title">FACTUUR</div>
                @endif
            </td>
            <td class="text-right" style="width: 50%;">
                <div class="company-name">{{ $company['name'] }}</div>
                <div class="contact-info">
                    {{ $company['address'] }}<br>
                    @if($company['email'])
                        {{ $company['email'] }}<br>
                    @endif
                    @if($company['phone'])
                        {{ $company['phone'] }}<br>
                    @endif
                </div>
                <div class="contact-info mt-2">
                    @if($company['kvk'])
                        KvK: {{ $company['kvk'] }}<br>
                    @endif
                    @if($company['btw'])
                        BTW: {{ $company['btw'] }}<br>
                    @endif
                    @if($company['iban'])
                        IBAN: {{ $company['iban'] }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

    {{-- Invoice title when logo is present --}}
    @if($logo_data)
    <div class="header-title mt-4">FACTUUR</div>
    @endif

    {{-- Invoice Details & Customer --}}
    <table class="mt-6">
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div class="section-title">Factuur aan</div>
                <div class="company-name">{{ $customer['name'] }}</div>
                <div class="contact-info">
                    @if($customer['address'])
                        {{ $customer['address'] }}<br>
                    @endif
                    @if($customer['email'])
                        {{ $customer['email'] }}<br>
                    @endif
                    @if($customer['phone'])
                        {{ $customer['phone'] }}
                    @endif
                </div>
            </td>
            <td style="width: 50%; vertical-align: top;" class="text-right">
                <table style="width: 100%;">
                    <tr>
                        <td class="invoice-label text-right" style="padding: 3px 0;">Factuurnummer</td>
                        <td class="bold text-right" style="padding: 3px 0; padding-left: 15px;">{{ $invoice_number }}</td>
                    </tr>
                    <tr>
                        <td class="invoice-label text-right" style="padding: 3px 0;">Factuurdatum</td>
                        <td class="text-right" style="padding: 3px 0; padding-left: 15px;">{{ $date }}</td>
                    </tr>
                    <tr>
                        <td class="invoice-label text-right" style="padding: 3px 0;">Vervaldatum</td>
                        <td class="text-right" style="padding: 3px 0; padding-left: 15px;">{{ $due_date }}</td>
                    </tr>
                    @if($payment_terms !== 'direct')
                    <tr>
                        <td class="invoice-label text-right" style="padding: 3px 0;">Betalingstermijn</td>
                        <td class="text-right" style="padding: 3px 0; padding-left: 15px;">{{ $payment_terms }} dagen</td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    {{-- Items Table --}}
    <table class="items-table">
        <thead>
            <tr>
                <th class="text-left" style="width: 50%;">Omschrijving</th>
                <th class="text-right" style="width: 15%;">Aantal</th>
                <th class="text-right" style="width: 17%;">Prijs</th>
                <th class="text-right" style="width: 18%;">Bedrag</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item['description'] }}</td>
                    <td class="text-right">{{ $item['quantity'] }}</td>
                    <td class="text-right">€ {{ number_format($item['price'], 2, ',', '.') }}</td>
                    <td class="text-right">€ {{ number_format($item['total'], 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <table class="totals-table">
        <tr>
            <td class="gray">Subtotaal</td>
            <td class="text-right">€ {{ number_format($subtotal, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="gray">BTW ({{ $vat_percentage }}%)</td>
            <td class="text-right">€ {{ number_format($vat_amount, 2, ',', '.') }}</td>
        </tr>
        <tr class="total-row">
            <td class="bold brand-color">Totaal</td>
            <td class="text-right bold brand-color">€ {{ number_format($total, 2, ',', '.') }}</td>
        </tr>
    </table>

    {{-- Notes --}}
    @if($notes)
    <div class="notes-section">
        <div class="bold mb-1">Opmerkingen:</div>
        {{ $notes }}
    </div>
    @endif

    {{-- Payment Info --}}
    <div class="payment-info">
        <div class="bold mb-1" style="color: {{ $brand_color }};">Betalingsinformatie</div>
        <div class="contact-info">
            Gelieve het bedrag van <strong>€ {{ number_format($total, 2, ',', '.') }}</strong>
            @if($payment_terms === 'direct')
                direct
            @else
                voor <strong>{{ $due_date }}</strong>
            @endif
            over te maken naar:
            <br><br>
            @if($company['iban'])
                <strong>IBAN:</strong> {{ $company['iban'] }}<br>
            @endif
            <strong>T.n.v.:</strong> {{ $company['name'] }}<br>
            <strong>O.v.v.:</strong> {{ $invoice_number }}
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <table>
            <tr>
                <td class="small gray">
                    {{ $company['name'] }}
                    @if($company['kvk']) | KvK: {{ $company['kvk'] }} @endif
                </td>
                <td class="small gray text-right">
                    Pagina 1 van 1
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
