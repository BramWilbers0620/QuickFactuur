<!DOCTYPE html>
<html lang="nl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Factuur {{ $invoice_number }}</title>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 50px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .brand-color { color: {{ $brand_color }}; }
        .light-gray { color: #999; }

        .logo {
            max-height: 50px;
            max-width: 150px;
        }

        .header-title {
            font-size: 36px;
            font-weight: 300;
            color: {{ $brand_color }};
            letter-spacing: -1px;
        }

        .company-name {
            font-size: 12px;
            font-weight: 600;
            color: #333;
        }

        .small-text {
            font-size: 9px;
            color: #666;
            line-height: 1.5;
        }

        .divider {
            height: 1px;
            background: #eee;
            margin: 30px 0;
        }

        .thin-divider {
            height: 1px;
            background: #f5f5f5;
            margin: 15px 0;
        }

        .meta-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #999;
            margin-bottom: 3px;
        }

        .meta-value {
            font-size: 11px;
            color: #333;
        }

        .items-table {
            margin: 30px 0;
        }

        .items-table th {
            font-size: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #999;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .items-table td {
            padding: 15px 0;
            border-bottom: 1px solid #f5f5f5;
            vertical-align: top;
        }

        .items-table .item-desc {
            font-size: 11px;
            color: #333;
        }

        .items-table .item-amount {
            font-size: 11px;
            color: #333;
        }

        .totals-table {
            width: 220px;
            margin-left: auto;
            margin-top: 20px;
        }

        .totals-table td {
            padding: 8px 0;
            font-size: 10px;
        }

        .totals-table .label {
            color: #999;
        }

        .totals-table .total-row td {
            padding-top: 15px;
            border-top: 1px solid #333;
            font-size: 14px;
            font-weight: 600;
        }

        .totals-table .total-row .brand-color {
            color: {{ $brand_color }};
        }

        .notes {
            margin-top: 40px;
            padding: 20px;
            background: #fafafa;
            font-size: 9px;
            color: #666;
        }

        .payment-box {
            margin-top: 40px;
            padding: 25px;
            background: #f8f8f8;
        }

        .payment-title {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: {{ $brand_color }};
            margin-bottom: 15px;
        }

        .payment-details {
            font-size: 10px;
            color: #555;
            line-height: 1.8;
        }

        .footer {
            margin-top: 60px;
            text-align: center;
        }

        .footer-text {
            font-size: 8px;
            color: #bbb;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <table>
        <tr>
            <td style="width: 50%; vertical-align: top;">
                @if($logo_data)
                    <img src="{{ $logo_data }}" class="logo" alt="Logo">
                    <div style="margin-top: 15px;">
                @else
                    <div>
                @endif
                    <div class="company-name">{{ $company['name'] }}</div>
                    <div class="small-text">
                        {{ $company['address'] }}<br>
                        @if($company['email']){{ $company['email'] }}@endif
                        @if($company['phone']) · {{ $company['phone'] }}@endif
                    </div>
                </div>
            </td>
            <td style="width: 50%;" class="text-right">
                <div class="header-title">factuur</div>
            </td>
        </tr>
    </table>

    <div class="divider"></div>

    {{-- Invoice Info --}}
    <table>
        <tr>
            <td style="width: 50%; vertical-align: top;">
                <div class="meta-label">Aan</div>
                <div class="company-name">{{ $customer['name'] }}</div>
                <div class="small-text">
                    @if($customer['address']){{ $customer['address'] }}<br>@endif
                    @if($customer['email']){{ $customer['email'] }}@endif
                </div>
            </td>
            <td style="width: 16%; vertical-align: top;">
                <div class="meta-label">Nummer</div>
                <div class="meta-value">{{ $invoice_number }}</div>
            </td>
            <td style="width: 17%; vertical-align: top;">
                <div class="meta-label">Datum</div>
                <div class="meta-value">{{ $date }}</div>
            </td>
            <td style="width: 17%; vertical-align: top;">
                <div class="meta-label">Vervalt</div>
                <div class="meta-value">{{ $due_date }}</div>
            </td>
        </tr>
    </table>

    {{-- Items --}}
    <table class="items-table">
        <thead>
            <tr>
                <th class="text-left" style="width: 55%;">Omschrijving</th>
                <th class="text-right" style="width: 15%;">Aantal</th>
                <th class="text-right" style="width: 15%;">Prijs</th>
                <th class="text-right" style="width: 15%;">Bedrag</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td class="item-desc">{{ $item['description'] }}</td>
                    <td class="text-right item-amount">{{ $item['quantity'] }}</td>
                    <td class="text-right item-amount">€{{ number_format($item['price'], 2, ',', '.') }}</td>
                    <td class="text-right item-amount">€{{ number_format($item['total'], 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals --}}
    <table class="totals-table">
        <tr>
            <td class="label">Subtotaal</td>
            <td class="text-right">€{{ number_format($subtotal, 2, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">BTW {{ $vat_percentage }}%</td>
            <td class="text-right">€{{ number_format($vat_amount, 2, ',', '.') }}</td>
        </tr>
        <tr class="total-row">
            <td class="brand-color">Totaal</td>
            <td class="text-right brand-color">€{{ number_format($total, 2, ',', '.') }}</td>
        </tr>
    </table>

    {{-- Notes --}}
    @if($notes)
    <div class="notes">
        {{ $notes }}
    </div>
    @endif

    {{-- Payment --}}
    <div class="payment-box">
        <div class="payment-title">Betaling</div>
        <div class="payment-details">
            Maak €{{ number_format($total, 2, ',', '.') }}
            @if($payment_terms !== 'direct')
                vóór {{ $due_date }}
            @endif
            over naar:<br>
            @if($company['iban']){{ $company['iban'] }}<br>@endif
            t.n.v. {{ $company['name'] }}<br>
            o.v.v. {{ $invoice_number }}
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-text">
            {{ $company['name'] }}
            @if($company['kvk']) · KvK {{ $company['kvk'] }} @endif
            @if($company['iban']) · {{ $company['iban'] }} @endif
        </div>
    </div>

</body>
</html>
