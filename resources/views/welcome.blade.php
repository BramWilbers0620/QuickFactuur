<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>QuickFactuur - Professionele Facturen in Seconden</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-text {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-gradient {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0e7ff 50%, #faf5ff 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        }
        .feature-icon {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        }
    </style>
</head>
<body class="antialiased bg-white">
    <!-- Navigation -->
    <nav class="fixed top-0 w-full bg-white/80 backdrop-blur-lg z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-gray-900">QuickFactuur</span>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 font-medium transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 font-medium transition-colors">Inloggen</a>
                        <a href="{{ route('register') }}" class="bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white px-5 py-2.5 rounded-full font-medium transition-all shadow-lg shadow-blue-500/25 hover:shadow-xl hover:shadow-blue-500/30">
                            Gratis Proberen
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-gradient pt-32 pb-20 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-4xl mx-auto">
                <div class="inline-flex items-center px-4 py-2 bg-blue-50 rounded-full mb-8">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                    <span class="text-sm font-medium text-blue-700">7 dagen gratis uitproberen</span>
                </div>

                <h1 class="text-5xl md:text-7xl font-extrabold text-gray-900 mb-6 leading-tight">
                    Facturen maken<br>
                    <span class="gradient-text">was nog nooit zo simpel</span>
                </h1>

                <p class="text-xl text-gray-600 mb-10 max-w-2xl mx-auto leading-relaxed">
                    Maak professionele facturen in seconden. Perfect voor ZZP'ers, freelancers en kleine ondernemers.
                    Automatische BTW-berekening en directe PDF download.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-semibold rounded-full transition-all shadow-lg shadow-blue-500/25 hover:shadow-xl hover:shadow-blue-500/30 text-lg">
                            Naar Dashboard
                            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-semibold rounded-full transition-all shadow-lg shadow-blue-500/25 hover:shadow-xl hover:shadow-blue-500/30 text-lg">
                            Start Gratis Trial
                            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </a>
                    @endauth
                    <a href="#pricing" class="inline-flex items-center justify-center px-8 py-4 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-full transition-all border border-gray-200 text-lg">
                        Bekijk Prijzen
                    </a>
                </div>

                <p class="mt-6 text-sm text-gray-500">
                    Geen creditcard nodig • Direct aan de slag • Annuleer wanneer je wilt
                </p>
            </div>

            <!-- Hero Image/Preview -->
            <div class="mt-16 relative">
                <div class="absolute inset-0 bg-gradient-to-t from-white via-transparent to-transparent z-10 pointer-events-none"></div>
                <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 p-4 max-w-4xl mx-auto">
                    <div class="bg-gray-50 rounded-xl p-8">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <div class="h-4 w-32 bg-gray-300 rounded mb-2"></div>
                                <div class="h-3 w-24 bg-gray-200 rounded"></div>
                            </div>
                            <div class="text-right">
                                <div class="h-6 w-28 bg-blue-100 rounded mb-2"></div>
                                <div class="h-3 w-20 bg-gray-200 rounded"></div>
                            </div>
                        </div>
                        <div class="border-t border-gray-200 pt-6">
                            <div class="flex justify-between items-center mb-4">
                                <div class="h-3 w-48 bg-gray-200 rounded"></div>
                                <div class="h-3 w-20 bg-gray-200 rounded"></div>
                            </div>
                            <div class="flex justify-between items-center mb-4">
                                <div class="h-3 w-36 bg-gray-200 rounded"></div>
                                <div class="h-3 w-16 bg-gray-200 rounded"></div>
                            </div>
                            <div class="border-t border-gray-200 pt-4 mt-4">
                                <div class="flex justify-between items-center">
                                    <div class="h-4 w-20 bg-gray-300 rounded"></div>
                                    <div class="h-5 w-24 bg-green-200 rounded"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Alles wat je nodig hebt
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Simpel, snel en professioneel factureren zonder gedoe
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="card-hover bg-white rounded-2xl p-8 border border-gray-100 shadow-sm">
                    <div class="feature-icon w-14 h-14 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Supersnel</h3>
                    <p class="text-gray-600 leading-relaxed">Maak een professionele factuur in minder dan 60 seconden. Vul je gegevens in en download direct je PDF.</p>
                </div>

                <div class="card-hover bg-white rounded-2xl p-8 border border-gray-100 shadow-sm">
                    <div class="feature-icon w-14 h-14 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Automatische BTW</h3>
                    <p class="text-gray-600 leading-relaxed">BTW wordt automatisch berekend volgens de Nederlandse wetgeving. Nooit meer rekenen of fouten maken.</p>
                </div>

                <div class="card-hover bg-white rounded-2xl p-8 border border-gray-100 shadow-sm">
                    <div class="feature-icon w-14 h-14 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Direct PDF</h3>
                    <p class="text-gray-600 leading-relaxed">Download je factuur direct als professionele PDF. Klaar om te versturen naar je klant.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-24 bg-gradient-to-b from-gray-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Simpele, eerlijke prijzen
                </h2>
                <p class="text-xl text-gray-600">
                    Start gratis, upgrade wanneer je wilt
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <!-- Monthly -->
                <div class="card-hover bg-white rounded-3xl p-8 border border-gray-200 shadow-sm">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Maandelijks</h3>
                        <p class="text-gray-500">Flexibel en vrijblijvend</p>
                    </div>

                    <div class="mb-6">
                        <span class="text-5xl font-extrabold text-gray-900">€5</span>
                        <span class="text-gray-500 text-lg">/maand</span>
                    </div>

                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Onbeperkt facturen
                        </li>
                        <li class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            PDF downloads
                        </li>
                        <li class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Automatische BTW
                        </li>
                        <li class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Maandelijks opzegbaar
                        </li>
                    </ul>

                    @auth
                        <a href="{{ route('billing') }}" class="block w-full text-center px-6 py-4 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold rounded-xl transition-colors">
                            Kies Maandelijks
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="block w-full text-center px-6 py-4 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold rounded-xl transition-colors">
                            Start met 7 dagen gratis
                        </a>
                    @endauth
                </div>

                <!-- Yearly - Featured -->
                <div class="card-hover bg-gradient-to-br from-blue-500 to-purple-600 rounded-3xl p-8 shadow-xl relative overflow-hidden">
                    <div class="absolute top-4 right-4">
                        <span class="bg-yellow-400 text-yellow-900 text-xs font-bold px-3 py-1 rounded-full">BESTE DEAL</span>
                    </div>

                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-white mb-2">Jaarlijks</h3>
                        <p class="text-blue-100">2 maanden gratis</p>
                    </div>

                    <div class="mb-6">
                        <span class="text-5xl font-extrabold text-white">€50</span>
                        <span class="text-blue-100 text-lg">/jaar</span>
                        <div class="mt-2">
                            <span class="text-blue-200 line-through text-sm">€60</span>
                            <span class="text-yellow-300 font-medium ml-2">Bespaar €10</span>
                        </div>
                    </div>

                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center text-white">
                            <svg class="w-5 h-5 text-yellow-300 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Alles van maandelijks
                        </li>
                        <li class="flex items-center text-white">
                            <svg class="w-5 h-5 text-yellow-300 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            €4,17 per maand
                        </li>
                        <li class="flex items-center text-white">
                            <svg class="w-5 h-5 text-yellow-300 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Prioriteit support
                        </li>
                        <li class="flex items-center text-white">
                            <svg class="w-5 h-5 text-yellow-300 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            17% korting
                        </li>
                    </ul>

                    @auth
                        <a href="{{ route('billing') }}" class="block w-full text-center px-6 py-4 bg-white hover:bg-gray-100 text-gray-900 font-semibold rounded-xl transition-colors shadow-lg">
                            Kies Jaarlijks
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="block w-full text-center px-6 py-4 bg-white hover:bg-gray-100 text-gray-900 font-semibold rounded-xl transition-colors shadow-lg">
                            Start met 7 dagen gratis
                        </a>
                    @endauth
                </div>
            </div>

            <p class="text-center text-gray-500 mt-8">
                Alle nieuwe accounts krijgen automatisch 7 dagen gratis toegang tot alle functies
            </p>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 bg-gray-900">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-white mb-6">
                Klaar om te beginnen?
            </h2>
            <p class="text-xl text-gray-400 mb-10">
                Maak vandaag nog je eerste factuur. Gratis, zonder verplichtingen.
            </p>
            @guest
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-10 py-4 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-semibold rounded-full transition-all shadow-lg text-lg">
                    Maak Gratis Account
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-10 py-4 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-semibold rounded-full transition-all shadow-lg text-lg">
                    Ga naar Dashboard
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
            @endguest
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 border-t border-gray-800 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center space-x-2 mb-4 md:mb-0">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-white">QuickFactuur</span>
                </div>
                <p class="text-gray-500 text-sm">
                    © {{ date('Y') }} QuickFactuur. Alle rechten voorbehouden.
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
