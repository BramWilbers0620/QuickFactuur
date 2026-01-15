<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - Server fout | {{ config('app.name', 'QuickFactuur') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-[Figtree] antialiased bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-lg w-full text-center">
        <div class="mb-8">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-red-100 mb-6">
                <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h1 class="text-7xl font-bold text-gray-900 mb-2">500</h1>
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Er ging iets mis</h2>
            <p class="text-gray-500 mb-8">
                Er is een onverwachte fout opgetreden op onze server. We zijn op de hoogte en werken aan een oplossing. Probeer het later opnieuw.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <button onclick="window.location.reload()" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Probeer opnieuw
            </button>
            <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-lg text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 transition-colors font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Naar homepagina
            </a>
        </div>

        <div class="mt-12 pt-8 border-t border-gray-200">
            <p class="text-sm text-gray-400">
                &copy; {{ date('Y') }} {{ config('app.name', 'QuickFactuur') }}
            </p>
        </div>
    </div>
</body>
</html>
