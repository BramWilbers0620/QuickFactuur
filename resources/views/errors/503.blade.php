<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Onderhoud | {{ config('app.name', 'QuickFactuur') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-[Figtree] antialiased bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-lg w-full text-center">
        <div class="mb-8">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-blue-100 mb-6">
                <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Even geduld...</h1>
            <h2 class="text-xl font-semibold text-gray-700 mb-4">We zijn bezig met onderhoud</h2>
            <p class="text-gray-500 mb-8">
                We werken hard om QuickFactuur nog beter te maken. We zijn zo snel mogelijk weer online. Bedankt voor je geduld!
            </p>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mb-8">
            <div class="flex items-center justify-center space-x-2 text-gray-600">
                <svg class="w-5 h-5 animate-spin text-blue-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Onderhoud bezig...</span>
            </div>
        </div>

        <div class="text-sm text-gray-500">
            <p>Heb je dringende vragen?</p>
            <a href="mailto:support@quickfactuur.nl" class="text-blue-600 hover:text-blue-700 font-medium">
                support@quickfactuur.nl
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
