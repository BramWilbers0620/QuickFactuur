<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
        <div class="container mx-auto px-4 py-12">

            @if(session('error'))
                <div class="max-w-3xl mx-auto mb-8">
                    <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl flex items-center shadow-sm">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="max-w-3xl mx-auto mb-8">
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl flex items-center shadow-sm">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                </div>
            @endif

            @if(request('cancelled'))
                <div class="max-w-3xl mx-auto mb-8">
                    <div class="bg-amber-50 border border-amber-200 text-amber-700 px-6 py-4 rounded-2xl flex items-center shadow-sm">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        Betaling geannuleerd. Je kunt het opnieuw proberen wanneer je wilt.
                    </div>
                </div>
            @endif

            @if(auth()->user()->subscribed('default'))
                {{-- Gebruiker heeft al een actief abonnement --}}
                <div class="max-w-2xl mx-auto">
                    <!-- Header -->
                    <div class="text-center mb-12">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-emerald-400 to-green-500 rounded-3xl mb-6 shadow-lg shadow-emerald-200">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h1 class="text-4xl font-bold text-slate-900 mb-3">
                            Je abonnement is <span class="text-emerald-600">actief</span>
                        </h1>
                        <p class="text-lg text-slate-600">
                            Bedankt voor je vertrouwen in QuickFactuur!
                        </p>
                    </div>

                    <!-- Current Subscription Card -->
                    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden mb-8">
                        <div class="bg-gradient-to-r from-emerald-500 to-green-500 px-8 py-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-emerald-100 text-sm font-medium">Huidig abonnement</p>
                                    <h2 class="text-2xl font-bold text-white mt-1">
                                        @if(auth()->user()->subscription('default')->stripe_price === config('services.stripe.plan_yearly'))
                                            Jaarlijks Abonnement
                                        @else
                                            Maandelijks Abonnement
                                        @endif
                                    </h2>
                                </div>
                                <div class="text-right">
                                    <p class="text-4xl font-bold text-white">
                                        @if(auth()->user()->subscription('default')->stripe_price === config('services.stripe.plan_yearly'))
                                            €50
                                        @else
                                            €5
                                        @endif
                                    </p>
                                    <p class="text-emerald-100 text-sm">
                                        @if(auth()->user()->subscription('default')->stripe_price === config('services.stripe.plan_yearly'))
                                            per jaar
                                        @else
                                            per maand
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="px-8 py-6">
                            <div class="grid grid-cols-2 gap-6 mb-6">
                                <div>
                                    <p class="text-sm text-slate-500">Status</p>
                                    <p class="text-lg font-semibold text-emerald-600 flex items-center mt-1">
                                        <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>
                                        Actief
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-slate-500">Volgende facturatie</p>
                                    <p class="text-lg font-semibold text-slate-900 mt-1">
                                        @if(auth()->user()->subscription('default')->ends_at)
                                            Eindigt op {{ auth()->user()->subscription('default')->ends_at->format('d-m-Y') }}
                                        @else
                                            Automatische verlenging
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="border-t border-slate-100 pt-6">
                                <h3 class="text-sm font-medium text-slate-700 mb-4">Je hebt toegang tot:</h3>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="flex items-center text-slate-600">
                                        <svg class="w-5 h-5 text-emerald-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Onbeperkt facturen
                                    </div>
                                    <div class="flex items-center text-slate-600">
                                        <svg class="w-5 h-5 text-emerald-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        PDF downloads
                                    </div>
                                    <div class="flex items-center text-slate-600">
                                        <svg class="w-5 h-5 text-emerald-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        BTW berekening
                                    </div>
                                    <div class="flex items-center text-slate-600">
                                        <svg class="w-5 h-5 text-emerald-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Automatische nummering
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('invoice.create') }}" class="flex-1 inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-2xl transition-all duration-200 shadow-lg shadow-blue-200 hover:shadow-xl hover:shadow-blue-300">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Nieuwe factuur maken
                        </a>

                        @if(!auth()->user()->subscription('default')->ends_at)
                            <form action="{{ route('subscription.cancel') }}" method="POST" class="flex-1" onsubmit="return confirm('Weet je zeker dat je je abonnement wilt opzeggen? Je houdt toegang tot het einde van je huidige factureringsperiode.')">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center px-6 py-4 border-2 border-slate-200 hover:border-red-300 hover:bg-red-50 text-slate-600 hover:text-red-600 font-semibold rounded-2xl transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Abonnement opzeggen
                                </button>
                            </form>
                        @else
                            <div class="flex-1 inline-flex items-center justify-center px-6 py-4 bg-amber-50 border-2 border-amber-200 text-amber-700 font-semibold rounded-2xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Eindigt op {{ auth()->user()->subscription('default')->ends_at->format('d-m-Y') }}
                            </div>
                        @endif
                    </div>
                </div>
            @else
                {{-- Gebruiker heeft nog geen abonnement - toon pricing --}}

                <!-- Header Section -->
                <div class="text-center mb-16">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-3xl mb-6 shadow-lg shadow-blue-200">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h1 class="text-4xl md:text-5xl font-bold text-slate-900 mb-4">
                        Kies je <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">abonnement</span>
                    </h1>
                    <p class="text-xl text-slate-600 max-w-2xl mx-auto">
                        @if(auth()->user()->trial_ends_at && auth()->user()->onGenericTrial())
                            Je trial loopt nog {{ (int) ceil(auth()->user()->trial_ends_at->diffInDays(now())) }} dagen. Upgrade nu voor ononderbroken toegang.
                        @elseif(auth()->user()->trial_ends_at)
                            Je trial is verlopen. Kies een abonnement om weer facturen te maken.
                        @else
                            Eenvoudig, transparant en zonder verborgen kosten.
                        @endif
                    </p>
                </div>

                <!-- Pricing Cards -->
                <div class="max-w-4xl mx-auto">
                    <div class="grid md:grid-cols-2 gap-8">

                        <!-- Monthly Plan -->
                        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden hover:shadow-2xl hover:shadow-slate-200/70 transition-all duration-300 group">
                            <div class="p-8">
                                <div class="flex items-center justify-between mb-6">
                                    <div>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                            Flexibel
                                        </span>
                                        <h3 class="text-2xl font-bold text-slate-900 mt-3">Maandelijks</h3>
                                    </div>
                                    <div class="w-14 h-14 bg-gradient-to-br from-blue-100 to-blue-50 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>

                                <div class="mb-8">
                                    <div class="flex items-baseline">
                                        <span class="text-5xl font-bold text-slate-900">€5</span>
                                        <span class="text-slate-500 ml-2 text-lg">per maand</span>
                                    </div>
                                    <p class="text-slate-500 mt-2">Maandelijks opzegbaar</p>
                                </div>

                                <ul class="space-y-4 mb-8">
                                    <li class="flex items-center text-slate-600">
                                        <div class="w-6 h-6 bg-emerald-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        Onbeperkt facturen maken
                                    </li>
                                    <li class="flex items-center text-slate-600">
                                        <div class="w-6 h-6 bg-emerald-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        Professionele PDF downloads
                                    </li>
                                    <li class="flex items-center text-slate-600">
                                        <div class="w-6 h-6 bg-emerald-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        Automatische BTW berekening
                                    </li>
                                    <li class="flex items-center text-slate-600">
                                        <div class="w-6 h-6 bg-emerald-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        Geen lange termijn verplichting
                                    </li>
                                </ul>

                                <form action="{{ route('subscribe') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="plan" value="monthly">
                                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-semibold py-4 px-6 rounded-xl transition-all duration-200 shadow-lg shadow-blue-200 hover:shadow-xl hover:shadow-blue-300 flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                        Kies maandelijks - €5/maand
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Yearly Plan (Featured) -->
                        <div class="bg-white rounded-3xl shadow-xl shadow-emerald-200/50 border-2 border-emerald-400 overflow-hidden hover:shadow-2xl hover:shadow-emerald-200/70 transition-all duration-300 group relative">
                            <!-- Popular Badge -->
                            <div class="absolute -top-px left-0 right-0">
                                <div class="bg-gradient-to-r from-emerald-500 to-green-500 text-white text-center py-2 px-4 rounded-t-3xl">
                                    <span class="text-sm font-bold flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                        Meest gekozen - Bespaar €10
                                    </span>
                                </div>
                            </div>

                            <div class="p-8 pt-14">
                                <div class="flex items-center justify-between mb-6">
                                    <div>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                            Beste waarde
                                        </span>
                                        <h3 class="text-2xl font-bold text-slate-900 mt-3">Jaarlijks</h3>
                                    </div>
                                    <div class="w-14 h-14 bg-gradient-to-br from-emerald-100 to-emerald-50 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>

                                <div class="mb-8">
                                    <div class="flex items-baseline">
                                        <span class="text-5xl font-bold text-slate-900">€50</span>
                                        <span class="text-slate-500 ml-2 text-lg">per jaar</span>
                                    </div>
                                    <p class="text-emerald-600 font-medium mt-2">= €4,17 per maand</p>
                                </div>

                                <ul class="space-y-4 mb-8">
                                    <li class="flex items-center text-slate-600">
                                        <div class="w-6 h-6 bg-emerald-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        Alles van maandelijks
                                    </li>
                                    <li class="flex items-center text-slate-600">
                                        <div class="w-6 h-6 bg-emerald-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <span class="font-semibold text-emerald-600">€10 per jaar besparen</span>
                                    </li>
                                    <li class="flex items-center text-slate-600">
                                        <div class="w-6 h-6 bg-emerald-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        Prioriteit ondersteuning
                                    </li>
                                    <li class="flex items-center text-slate-600">
                                        <div class="w-6 h-6 bg-emerald-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        Een jaar zorgeloos factureren
                                    </li>
                                </ul>

                                <form action="{{ route('subscribe') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="plan" value="yearly">
                                    <button type="submit" class="w-full bg-gradient-to-r from-emerald-500 to-green-500 hover:from-emerald-600 hover:to-green-600 text-white font-semibold py-4 px-6 rounded-xl transition-all duration-200 shadow-lg shadow-emerald-200 hover:shadow-xl hover:shadow-emerald-300 flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                        Kies jaarlijks - €50/jaar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods Info -->
                <div class="mt-12 max-w-4xl mx-auto">
                    <div class="bg-white/60 backdrop-blur-sm rounded-2xl border border-slate-200/50 p-6">
                        <div class="text-center mb-4">
                            <p class="text-sm font-medium text-slate-700">Betaal veilig met</p>
                        </div>
                        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: 40px;">
                            <img src="/foto/ideal.png" alt="iDEAL" style="height: 48px; width: auto;">
                            <img src="/foto/bancontact.png" alt="Bancontact" style="height: 48px; width: auto;">
                            <img src="/foto/visa.png" alt="Visa" style="height: 32px; width: auto;">
                            <img src="/foto/mastercard.png" alt="Mastercard" style="height: 32px; width: auto;">
                            <img src="/foto/American Express.png" alt="American Express" style="height: 32px; width: auto;">
                        </div>
                    </div>
                </div>

                <!-- Trust Section -->
                <div class="mt-8 max-w-4xl mx-auto">
                    <div class="bg-white/60 backdrop-blur-sm rounded-2xl border border-slate-200/50 p-8">
                        <div class="grid md:grid-cols-3 gap-8 text-center">
                            <div>
                                <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-100 rounded-xl mb-4">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <h4 class="font-semibold text-slate-900 mb-1">Veilig betalen</h4>
                                <p class="text-sm text-slate-600">Via Stripe, 's werelds meest vertrouwde betalingsplatform</p>
                            </div>
                            <div>
                                <div class="inline-flex items-center justify-center w-12 h-12 bg-emerald-100 rounded-xl mb-4">
                                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                                <h4 class="font-semibold text-slate-900 mb-1">Geen verborgen kosten</h4>
                                <p class="text-sm text-slate-600">Wat je ziet is wat je betaalt</p>
                            </div>
                            <div>
                                <div class="inline-flex items-center justify-center w-12 h-12 bg-purple-100 rounded-xl mb-4">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <h4 class="font-semibold text-slate-900 mb-1">Altijd opzegbaar</h4>
                                <p class="text-sm text-slate-600">Stop wanneer je wilt, geen vragen</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
