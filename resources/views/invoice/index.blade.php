<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-slate-900">Mijn Facturen</h1>
                    <p class="text-slate-600 mt-1">Bekijk al je gemaakte facturen</p>
                </div>
                <a href="{{ route('invoice.create') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-blue-200 hover:shadow-xl group">
                    <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Nieuwe Factuur
                </a>
            </div>

            @if(session('success'))
                <div class="mb-6">
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl flex items-center shadow-sm">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            <!-- Search and Filter -->
            <div class="bg-white rounded-2xl shadow-lg shadow-slate-200/50 border border-slate-100 p-4 mb-6">
                <form method="GET" action="{{ route('invoice.index') }}" class="flex flex-col md:flex-row gap-4">
                    <!-- Search Input -->
                    <div class="flex-1">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Zoek op factuurnummer, klant of email..." class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="w-full md:w-48">
                        <select name="status" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">Alle statussen</option>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div class="flex gap-2">
                        <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="Van" class="w-full md:w-36 px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm">
                        <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="Tot" class="w-full md:w-36 px-3 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm">
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-2">
                        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-colors">
                            Zoeken
                        </button>
                        @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                            <a href="{{ route('invoice.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-xl transition-colors">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            @if($invoices->isEmpty() && !request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                <!-- Empty State -->
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 p-12 text-center">
                    <div class="mx-auto w-20 h-20 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-3xl flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-3">Nog geen facturen</h3>
                    <p class="text-slate-600 mb-8 max-w-md mx-auto">Je hebt nog geen facturen aangemaakt. Begin met je eerste factuur en houd je administratie makkelijk bij.</p>
                    <a href="{{ route('invoice.create') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-2xl transition-all duration-200 shadow-lg shadow-blue-200 hover:shadow-xl group">
                        <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Maak je eerste factuur
                    </a>
                </div>
            @elseif($invoices->isEmpty())
                <!-- No Results State -->
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 p-12 text-center">
                    <div class="mx-auto w-20 h-20 bg-gradient-to-br from-amber-100 to-orange-100 rounded-3xl flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-3">Geen facturen gevonden</h3>
                    <p class="text-slate-600 mb-8 max-w-md mx-auto">Er zijn geen facturen die voldoen aan je zoekcriteria. Probeer andere filters.</p>
                    <a href="{{ route('invoice.index') }}" class="inline-flex items-center px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-xl transition-colors">
                        Filters wissen
                    </a>
                </div>
            @else
                <!-- Invoice List -->
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                    <!-- Desktop Table -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100">
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                        Factuurnummer
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                        Klant
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                        Datum
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                        Bedrag
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">
                                        Acties
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($invoices as $invoice)
                                    <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                        <td class="px-6 py-5 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-bold text-slate-900">{{ $invoice->invoice_number }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 whitespace-nowrap">
                                            <div class="text-sm font-medium text-slate-900">{{ $invoice->customer_name }}</div>
                                        </td>
                                        <td class="px-6 py-5 whitespace-nowrap">
                                            <div class="text-sm text-slate-600">{{ $invoice->invoice_date->format('d-m-Y') }}</div>
                                        </td>
                                        <td class="px-6 py-5 whitespace-nowrap">
                                            <div class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200">
                                                <span class="text-sm font-bold text-emerald-700">€{{ number_format($invoice->total, 2, ',', '.') }}</span>
                                            </div>
                                            <div class="text-xs text-slate-500 mt-1">incl. BTW</div>
                                        </td>
                                        <td class="px-6 py-5 whitespace-nowrap">
                                            @php
                                                $status = $invoice->status ?? 'concept';
                                                $statusColors = [
                                                    'concept' => 'bg-slate-100 text-slate-700 border-slate-200',
                                                    'verzonden' => 'bg-blue-100 text-blue-700 border-blue-200',
                                                    'betaald' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                                    'te_laat' => 'bg-red-100 text-red-700 border-red-200',
                                                ];
                                                $statusLabels = [
                                                    'concept' => 'Concept',
                                                    'verzonden' => 'Verzonden',
                                                    'betaald' => 'Betaald',
                                                    'te_laat' => 'Te laat',
                                                ];
                                            @endphp
                                            <select onchange="updateStatus({{ $invoice->id }}, this.value)"
                                                    class="text-sm font-medium px-3 py-1.5 rounded-full border cursor-pointer {{ $statusColors[$status] }}">
                                                @foreach($statusLabels as $value => $label)
                                                    <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-6 py-5 whitespace-nowrap text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                @if($invoice->pdf_path)
                                                    <a href="{{ route('invoice.download', $invoice) }}"
                                                       class="inline-flex items-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 text-sm font-medium rounded-lg transition-colors"
                                                       title="Download PDF">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                    </a>
                                                @endif
                                                <a href="{{ route('invoice.duplicate', $invoice) }}"
                                                   class="inline-flex items-center px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-600 text-sm font-medium rounded-lg transition-colors"
                                                   title="Dupliceren">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                    </svg>
                                                </a>
                                                @if($invoice->customer_email)
                                                    <button onclick="sendEmail({{ $invoice->id }})"
                                                            class="inline-flex items-center px-3 py-1.5 bg-purple-50 hover:bg-purple-100 text-purple-600 text-sm font-medium rounded-lg transition-colors"
                                                            title="Verstuur per e-mail">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Cards -->
                    <div class="md:hidden divide-y divide-slate-100">
                        @foreach($invoices as $invoice)
                            <div class="p-6 hover:bg-slate-50/50 transition-colors duration-150">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-bold text-slate-900">{{ $invoice->invoice_number }}</div>
                                            <div class="text-xs text-slate-500">{{ $invoice->invoice_date->format('d-m-Y') }}</div>
                                        </div>
                                    </div>
                                    <div class="inline-flex items-center px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200">
                                        <span class="text-sm font-bold text-emerald-700">€{{ number_format($invoice->total, 2, ',', '.') }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="text-sm text-slate-600">{{ $invoice->customer_name }}</div>
                                    <div class="text-xs text-slate-500">{{ $invoice->created_at->format('d-m-Y H:i') }}</div>
                                </div>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @if($invoice->pdf_path)
                                        <a href="{{ route('invoice.download', $invoice) }}"
                                           class="inline-flex items-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 text-sm font-medium rounded-lg transition-colors">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            Download
                                        </a>
                                    @endif
                                    <a href="{{ route('invoice.duplicate', $invoice) }}"
                                       class="inline-flex items-center px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-600 text-sm font-medium rounded-lg transition-colors">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        Dupliceren
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pagination -->
                @if($invoices->hasPages())
                    <div class="mt-8">
                        {{ $invoices->links() }}
                    </div>
                @endif

                <!-- Summary -->
                <div class="mt-8 bg-white/60 backdrop-blur-sm rounded-2xl border border-slate-200/50 p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-slate-600">Totaal aantal facturen</p>
                                <p class="text-2xl font-bold text-slate-900">{{ $invoices->total() }}</p>
                            </div>
                        </div>
                        <a href="{{ route('invoice.create') }}" class="hidden sm:inline-flex items-center px-4 py-2 text-blue-600 hover:text-blue-700 font-medium transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Nieuwe factuur
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function updateStatus(invoiceId, status) {
            fetch(`/facturen/${invoiceId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Refresh page to show updated colors
                    location.reload();
                } else {
                    alert('Er ging iets mis bij het updaten van de status.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Er ging iets mis bij het updaten van de status.');
            });
        }

        function sendEmail(invoiceId) {
            if (!confirm('Wil je deze factuur per e-mail versturen naar de klant?')) {
                return;
            }

            const button = event.target.closest('button');
            button.disabled = true;
            button.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

            fetch(`/facturen/${invoiceId}/email`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Factuur is succesvol verzonden per e-mail!');
                    location.reload();
                } else {
                    alert(data.message || 'Er ging iets mis bij het versturen van de e-mail.');
                    button.disabled = false;
                    button.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Er ging iets mis bij het versturen van de e-mail.');
                button.disabled = false;
                button.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>';
            });
        }
    </script>
</x-app-layout>
