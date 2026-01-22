<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                <div class="mb-4 sm:mb-0">
                    <a href="{{ route('invoice.index') }}" class="inline-flex items-center text-slate-600 hover:text-slate-900 mb-2">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Terug naar overzicht
                    </a>
                    <h1 class="text-3xl font-bold text-slate-900">Factuur {{ $invoice->invoice_number }}</h1>
                    <p class="text-slate-600 mt-1">Aangemaakt op {{ $invoice->created_at->format('d-m-Y H:i') }}</p>
                </div>
                <div class="flex gap-2">
                    @if($invoice->pdf_path)
                        <a href="{{ route('invoice.download', $invoice) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download PDF
                        </a>
                    @endif
                    <a href="{{ route('invoice.duplicate', $invoice) }}" class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-xl transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Dupliceren
                    </a>
                </div>
            </div>

            <!-- Status Badge -->
            <div class="mb-6">
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium border {{ $invoice->status_color }}">
                    {{ $invoice->status_label }}
                </span>
                @if($invoice->isOverdue())
                    <span class="ml-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 border border-red-200">
                        Te laat
                    </span>
                @endif
            </div>

            <!-- Main Content -->
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                <!-- Invoice Details Grid -->
                <div class="grid md:grid-cols-2 gap-8 p-8">
                    <!-- Company Info -->
                    <div>
                        <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4">Van</h3>
                        <div class="space-y-1">
                            <p class="text-lg font-semibold text-slate-900">{{ $invoice->company_name }}</p>
                            @if($invoice->company_address)
                                <p class="text-slate-600 whitespace-pre-line">{{ $invoice->company_address }}</p>
                            @endif
                            @if($invoice->company_email)
                                <p class="text-slate-600">{{ $invoice->company_email }}</p>
                            @endif
                            @if($invoice->company_phone)
                                <p class="text-slate-600">{{ $invoice->company_phone }}</p>
                            @endif
                            @if($invoice->company_kvk)
                                <p class="text-slate-500 text-sm">KVK: {{ $invoice->company_kvk }}</p>
                            @endif
                            @if($invoice->company_vat)
                                <p class="text-slate-500 text-sm">BTW: {{ $invoice->company_vat }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div>
                        <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4">Aan</h3>
                        <div class="space-y-1">
                            <p class="text-lg font-semibold text-slate-900">{{ $invoice->customer_name }}</p>
                            @if($invoice->customer_address)
                                <p class="text-slate-600 whitespace-pre-line">{{ $invoice->customer_address }}</p>
                            @endif
                            @if($invoice->customer_email)
                                <p class="text-slate-600">{{ $invoice->customer_email }}</p>
                            @endif
                            @if($invoice->customer_phone)
                                <p class="text-slate-600">{{ $invoice->customer_phone }}</p>
                            @endif
                            @if($invoice->customer_vat)
                                <p class="text-slate-500 text-sm">BTW: {{ $invoice->customer_vat }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Invoice Meta -->
                <div class="bg-slate-50 border-y border-slate-100 px-8 py-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase">Factuurnummer</p>
                            <p class="text-slate-900 font-medium">{{ $invoice->invoice_number }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase">Factuurdatum</p>
                            <p class="text-slate-900 font-medium">{{ $invoice->invoice_date->format('d-m-Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase">Vervaldatum</p>
                            <p class="text-slate-900 font-medium {{ $invoice->isOverdue() ? 'text-red-600' : '' }}">
                                {{ $invoice->due_date->format('d-m-Y') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase">Betalingstermijn</p>
                            <p class="text-slate-900 font-medium">
                                {{ $invoice->payment_terms === 'direct' ? 'Direct' : $invoice->payment_terms . ' dagen' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="p-8">
                    <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4">Regels</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-slate-200">
                                    <th class="text-left py-3 text-xs font-bold text-slate-500 uppercase">Omschrijving</th>
                                    <th class="text-right py-3 text-xs font-bold text-slate-500 uppercase">Aantal</th>
                                    <th class="text-right py-3 text-xs font-bold text-slate-500 uppercase">Prijs</th>
                                    <th class="text-right py-3 text-xs font-bold text-slate-500 uppercase">Totaal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($invoice->items as $item)
                                    <tr>
                                        <td class="py-4 text-slate-900">{{ $item['description'] }}</td>
                                        <td class="py-4 text-slate-600 text-right">{{ $item['quantity'] }}</td>
                                        <td class="py-4 text-slate-600 text-right">{{ number_format($item['price'], 2, ',', '.') }}</td>
                                        <td class="py-4 text-slate-900 font-medium text-right">{{ number_format($item['total'], 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Totals -->
                    <div class="mt-6 border-t border-slate-200 pt-6">
                        <div class="flex justify-end">
                            <div class="w-64 space-y-2">
                                <div class="flex justify-between text-slate-600">
                                    <span>Subtotaal</span>
                                    <span>{{ number_format($invoice->amount, 2, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-slate-600">
                                    <span>BTW ({{ $invoice->vat_rate }}%)</span>
                                    <span>{{ number_format($invoice->vat_amount, 2, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-xl font-bold text-slate-900 pt-2 border-t border-slate-200">
                                    <span>Totaal</span>
                                    <span>{{ number_format($invoice->total, 2, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                @if($invoice->notes)
                    <div class="bg-slate-50 border-t border-slate-100 px-8 py-6">
                        <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-2">Opmerkingen</h3>
                        <p class="text-slate-600 whitespace-pre-line">{{ $invoice->notes }}</p>
                    </div>
                @endif

                <!-- Timestamps -->
                <div class="bg-slate-50 border-t border-slate-100 px-8 py-4">
                    <div class="flex flex-wrap gap-6 text-sm text-slate-500">
                        @if($invoice->sent_at)
                            <div>
                                <span class="font-medium">Verzonden:</span>
                                {{ $invoice->sent_at->format('d-m-Y H:i') }}
                            </div>
                        @endif
                        @if($invoice->paid_at)
                            <div>
                                <span class="font-medium">Betaald:</span>
                                {{ $invoice->paid_at->format('d-m-Y H:i') }}
                            </div>
                        @endif
                        @if($invoice->company_iban)
                            <div>
                                <span class="font-medium">IBAN:</span>
                                {{ $invoice->company_iban }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
