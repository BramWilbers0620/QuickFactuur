<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

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

            @if(session('error'))
                <div class="mb-6">
                    <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl flex items-center shadow-sm">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <!-- Welcome Header -->
            <div class="mb-8">
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-8">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mr-6">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h1 class="text-3xl font-bold text-white mb-1">
                                        Welkom terug, {{ auth()->user()->name }}!
                                    </h1>
                                    <p class="text-blue-100">Beheer je facturen, offertes en klanten</p>
                                </div>
                            </div>
                            <div class="hidden md:block">
                                @if(auth()->user()->onGenericTrial())
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-blue-500/30 text-white backdrop-blur-sm">
                                        <span class="w-2 h-2 bg-blue-200 rounded-full mr-2 animate-pulse"></span>
                                        Trial tot {{ auth()->user()->trial_ends_at->format('d-m-Y') }}
                                    </span>
                                @elseif(auth()->user()->subscribed('default'))
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-emerald-500/30 text-white backdrop-blur-sm">
                                        <span class="w-2 h-2 bg-emerald-300 rounded-full mr-2 animate-pulse"></span>
                                        Actief abonnement
                                    </span>
                                @elseif(auth()->user()->isAdmin())
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-purple-500/30 text-white backdrop-blur-sm">
                                        <span class="w-2 h-2 bg-purple-300 rounded-full mr-2 animate-pulse"></span>
                                        Admin Account
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($stats)
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Revenue -->
                <div class="bg-white rounded-2xl shadow-lg shadow-slate-200/50 border border-slate-100 p-6 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 mb-1">Totale Omzet</p>
                            <p class="text-2xl font-bold text-slate-900">€{{ number_format($stats['totalRevenue'], 0, ',', '.') }}</p>
                            <p class="text-sm text-emerald-600 mt-1">
                                €{{ number_format($stats['revenueThisMonth'], 0, ',', '.') }} deze maand
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-green-100 rounded-2xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Invoices -->
                <div class="bg-white rounded-2xl shadow-lg shadow-slate-200/50 border border-slate-100 p-6 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 mb-1">Facturen</p>
                            <p class="text-2xl font-bold text-slate-900">{{ $stats['totalInvoices'] }}</p>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-sm text-emerald-600">{{ $stats['paidInvoices'] }} betaald</span>
                                @if($stats['overdueInvoices'] > 0)
                                    <span class="text-sm text-red-600">{{ $stats['overdueInvoices'] }} te laat</span>
                                @endif
                            </div>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-2xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Quotes -->
                <div class="bg-white rounded-2xl shadow-lg shadow-slate-200/50 border border-slate-100 p-6 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 mb-1">Offertes</p>
                            <p class="text-2xl font-bold text-slate-900">{{ $stats['totalQuotes'] }}</p>
                            <div class="flex items-center gap-3 mt-1">
                                <span class="text-sm text-blue-600">{{ $stats['pendingQuotes'] }} open</span>
                                <span class="text-sm text-emerald-600">{{ $stats['acceptedQuotes'] }} geaccepteerd</span>
                            </div>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-br from-amber-100 to-orange-100 rounded-2xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Customers -->
                <div class="bg-white rounded-2xl shadow-lg shadow-slate-200/50 border border-slate-100 p-6 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-500 mb-1">Klanten</p>
                            <p class="text-2xl font-bold text-slate-900">{{ $stats['totalCustomers'] }}</p>
                            <p class="text-sm text-slate-600 mt-1">In je klantenbestand</p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-pink-100 rounded-2xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Chart & Recent Invoices -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Revenue Chart -->
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 p-6">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Omzet laatste 6 maanden</h3>
                    <div class="h-48 flex items-end justify-between gap-2">
                        @foreach($stats['monthlyRevenue'] as $month)
                            @php
                                $maxRevenue = max(array_column($stats['monthlyRevenue'], 'revenue'));
                                $height = $maxRevenue > 0 ? ($month['revenue'] / $maxRevenue) * 100 : 0;
                            @endphp
                            <div class="flex-1 flex flex-col items-center">
                                <div class="w-full bg-gradient-to-t from-blue-500 to-indigo-500 rounded-t-lg transition-all duration-300 hover:from-blue-600 hover:to-indigo-600"
                                     style="height: {{ max($height, 5) }}%"
                                     title="€{{ number_format($month['revenue'], 0, ',', '.') }}">
                                </div>
                                <span class="text-xs text-slate-500 mt-2">{{ $month['month'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Recent Invoices -->
                <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-slate-900">Recente Facturen</h3>
                        <a href="{{ route('invoice.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                            Bekijk alle →
                        </a>
                    </div>
                    @if($stats['recentInvoices']->isEmpty())
                        <div class="text-center py-8 text-slate-500">
                            <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p>Nog geen facturen</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($stats['recentInvoices'] as $invoice)
                                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-slate-900">{{ $invoice->invoice_number }}</p>
                                            <p class="text-sm text-slate-500">{{ $invoice->customer_name }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-slate-900">€{{ number_format($invoice->total, 2, ',', '.') }}</p>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border {{ $invoice->status_color }}">
                                            {{ $invoice->status_label }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- New Invoice -->
                <a href="{{ route('invoice.create') }}" class="bg-white rounded-2xl shadow-lg shadow-slate-200/50 border border-slate-100 p-6 hover:shadow-xl hover:border-blue-200 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">Nieuwe Factuur</h3>
                    <p class="text-slate-600 text-sm">Maak een professionele factuur</p>
                </a>

                <!-- New Quote -->
                <a href="{{ route('quotes.create') }}" class="bg-white rounded-2xl shadow-lg shadow-slate-200/50 border border-slate-100 p-6 hover:shadow-xl hover:border-emerald-200 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-green-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">Nieuwe Offerte</h3>
                    <p class="text-slate-600 text-sm">Stuur een offerte naar je klant</p>
                </a>

                <!-- View Customers -->
                <a href="{{ route('customers.index') }}" class="bg-white rounded-2xl shadow-lg shadow-slate-200/50 border border-slate-100 p-6 hover:shadow-xl hover:border-purple-200 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">Klanten</h3>
                    <p class="text-slate-600 text-sm">Beheer je klantenbestand</p>
                </a>

                <!-- Profile -->
                <a href="{{ route('profile.edit') }}" class="bg-white rounded-2xl shadow-lg shadow-slate-200/50 border border-slate-100 p-6 hover:shadow-xl hover:border-amber-200 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-1">Instellingen</h3>
                    <p class="text-slate-600 text-sm">Profiel & bedrijfsgegevens</p>
                </a>
            </div>

            @if(auth()->user()->trial_ends_at && !auth()->user()->subscribed('default') && !auth()->user()->isAdmin())
                @php
                    $daysLeft = (int) ceil(now()->diffInDays(auth()->user()->trial_ends_at, false));
                    $isTrialExpired = $daysLeft <= 0;
                @endphp

                <!-- Trial Notice -->
                <div class="mt-8">
                    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border {{ $isTrialExpired ? 'border-red-200' : 'border-blue-200' }} overflow-hidden">
                        <div class="bg-gradient-to-r {{ $isTrialExpired ? 'from-red-500 to-orange-500' : 'from-blue-500 to-indigo-500' }} px-8 py-4">
                            <div class="flex items-center">
                                @if($isTrialExpired)
                                    <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                    <h3 class="text-lg font-bold text-white">Je gratis trial is verlopen</h3>
                                @else
                                    <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <h3 class="text-lg font-bold text-white">Je gratis trial is actief</h3>
                                @endif
                            </div>
                        </div>
                        <div class="p-8">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="mb-4 md:mb-0">
                                    @if($isTrialExpired)
                                        <p class="text-slate-700">Je trial is <span class="font-semibold text-red-600">{{ abs($daysLeft) }} {{ abs($daysLeft) == 1 ? 'dag' : 'dagen' }}</span> geleden verlopen.</p>
                                        <p class="text-slate-600 mt-1">Kies nu een abonnement om weer facturen te kunnen maken.</p>
                                    @else
                                        <p class="text-slate-700">Je trial loopt nog <span class="font-semibold text-blue-600">{{ $daysLeft }} {{ $daysLeft == 1 ? 'dag' : 'dagen' }}</span>.</p>
                                        <p class="text-slate-600 mt-1">Kies een abonnement voor ononderbroken toegang na je trial.</p>
                                    @endif
                                </div>
                                <a href="{{ route('billing') }}" class="inline-flex items-center justify-center px-6 py-3 {{ $isTrialExpired ? 'bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 shadow-red-200' : 'bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-blue-200' }} text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                    </svg>
                                    {{ $isTrialExpired ? 'Kies abonnement nu' : 'Bekijk abonnementen' }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
