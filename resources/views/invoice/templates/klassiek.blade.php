<!DOCTYPE html>
<html lang="nl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Factuur {{ $invoice_number }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11px;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 40px 50px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .brand-color { color: {{ $brand_color }}; }
        .gray { color: #666; }
        .small { font-size: 9px; }

        .header {
            border-bottom: 3px double {{ $brand_color }};
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header-title {
            font-size: 32px;
            font-weight: bold;
            color: {{ $brand_color }};
            font-style: italic;
            letter-spacing: 2px;
        }

        .logo {
            max-height: 70px;
            max-width: 200px;
        }

        .company-block {
            margin-top: 15px;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .address-line {
            font-size: 10px;
            color: #555;
            line-height: 1.6;
        }

        .invoice-meta {
            background: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            margin: 25px 0;
        }

        .invoice-meta-row {
            margin-bottom: 8px;
        }

        .invoice-meta-label {
            font-weight: bold;
            color: #555;
            display: inline-block;
            width: 120px;
        }

        .customer-section {
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 25px;
        }

        .section-header {
            font-size: 12px;
            font-weight: bold;
            color: {{ $brand_color }};
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .items-table {
            margin: 25px 0;
        }

        .items-table th {
            background: {{ $brand_color }};
            color: white;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 12px 10px;
            border: 1px solid {{ $brand_color }};
        }

        .items-table td {
            padding: 12px 10px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        .items-table tr:nth-child(even) {
            background: #f9f9f9;
        }

        .totals-section {
            margin-top: 20px;
            border-top: 2px solid #ddd;
            padding-top: 15px;
        }

        .totals-table {
            width: 300px;
            margin-left: auto;
        }

        .totals-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #eee;
        }

        .totals-table .total-row {
            background: {{ $brand_color }};
            color: white;
            font-size: 14px;
            font-weight: bold;
        }

        .totals-table .total-row td {
            border: none;
            padding: 12px 10px;
        }

        .notes-section {
            margin-top: 30px;
            padding: 15px;
            border: 1px dashed #ccc;
            background: #fffef0;
        }

        .payment-section {
            margin-top: 30px;
            padding: 20px;
            border: 2px solid {{ $brand_color }};
            background: #f8f9fa;
        }

        .payment-title {
            font-size: 14px;
            font-weight: bold;
            color: {{ $brand_color }};
            margin-bottom: 10px;
        }

        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 3px double {{ $brand_color }};
            text-align: center;
        }

        .footer-text {
            font-size: 9px;
            color: #888;
            font-style: italic;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <table>
            <tr>
                <td style="width: 60%;">
                    @if($logo_data)
                        <img src="{{ $logo_data }}" class="logo" alt="Logo">
                    @endif
                    <div class="company-block">
                        <div class="company-name">{{ $company['name'] }}</div>
                        <div class="address-line">
                            {{ $company['address'] }}<br>
                            @if($company['email']){{ $company['email'] }}<br>@endif
                            @if($company['phone'])Tel: {{ $company['phone'] }}@endif
                        </div>
                    </div>
                </td>
                <td style="width: 40%;" class="text-right">
                    <div class="header-title">FACTUUR</div>
                    <div class="address-line" style="margin-top: 10px;">
                        @if($company['kvk'])KvK: {{ $company['kvk'] }}<br>@endif
                        @if($company['iban'])IBAN: {{ $company['iban'] }}@endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Invoice Meta --}}
    <table>
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div class="customer-section">
                    <div class="section-header">Factuur aan</div>
                    <div class="company-name">{{ $customer['name'] }}</div>
                    <div class="address-line">
                        @if($customer['address']){{ $customer['address'] }}<br>@endif
                        @if($customer['email']){{ $customer['email'] }}<br>@endif
                        @if($customer['phone'])Tel: {{ $customer['phone'] }}@endif
                    </div>
                </div>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 20px;">
                <div class="invoice-meta">
                    <div class="invoice-meta-row">
                        <span class="invoice-meta-label">Factuurnummer:</span>
                        <strong>{{ $invoice_number }}</strong>
                    </div>
                    <div class="invoice-meta-row">
                        <span class="invoice-meta-label">Factuurdatum:</span>
                        {{ $date }}
                    </div>
                    <div class="invoice-meta-row">
                        <span class="invoice-meta-label">Vervaldatum:</span>
                        {{ $due_date }}
                    </div>
                    @if($payment_terms !== 'direct')
                    <div class="invoice-meta-row">
                        <span class="invoice-meta-label">Betalingstermijn:</span>
                        {{ $payment_terms }} dagen
                    </div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    {{-- Items Table --}}
    <table class="items-table">
        <thead>
            <tr>
                <th class="text-left" style="width: 50%;">Omschrijving</th>
                <th class="text-center" style="width: 15%;">Aantal</th>
                <th class="text-right" style="width: 17%;">Prijs</th>
                <th class="text-right" style="width: 18%;">Bedrag</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item['description'] }}</td>
                    <td class="text-center">{{ $item['quantity'] }}</td>
                    <td class="text-right">€ {{ number_format($item['price'], 2, ',', '.') }}</td>
                    <td class="text-right">€ {{ number_format($item['total'], 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <div class="totals-section">
        <table class="totals-table">
            <tr>
                <td>Subtotaal</td>
                <td class="text-right">€ {{ number_format($subtotal, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td>BTW ({{ $vat_percentage }}%)</td>
                <td class="text-right">€ {{ number_format($vat_amount, 2, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td>TOTAAL</td>
                <td class="text-right">€ {{ number_format($total, 2, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    {{-- Notes --}}
    @if($notes)
    <div class="notes-section">
        <strong>Opmerkingen:</strong><br>
        {{ $notes }}
    </div>
    @endif

    {{-- Payment Info --}}
    <div class="payment-section">
        <div class="payment-title">Betalingsgegevens</div>
        <div class="address-line">
            Gelieve het totaalbedrag van <strong>€ {{ number_format($total, 2, ',', '.') }}</strong>
            @if($payment_terms === 'direct')
                direct
            @else
                vóór <strong>{{ $due_date }}</strong>
            @endif
            over te maken naar:<br><br>
            @if($company['iban'])<strong>IBAN:</strong> {{ $company['iban'] }}<br>@endif
            <strong>Ten name van:</strong> {{ $company['name'] }}<br>
            <strong>Onder vermelding van:</strong> {{ $invoice_number }}
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-text">
            {{ $company['name'] }}
            @if($company['kvk']) • KvK: {{ $company['kvk'] }} @endif
            @if($company['email']) • {{ $company['email'] }} @endif
        </div>
        <div class="footer-text" style="margin-top: 5px;">
            Bedankt voor uw vertrouwen in onze diensten
        </div>
    </div>

</body>
</html>
