<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Onderhoud | {{ config('app.name', 'QuickFactuur') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #eff6ff 0%, #e0e7ff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            -webkit-font-smoothing: antialiased;
        }
        .container { max-width: 28rem; width: 100%; text-align: center; }
        .icon-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 6rem;
            height: 6rem;
            border-radius: 50%;
            background-color: #dbeafe;
            margin-bottom: 1.5rem;
        }
        .icon { width: 3rem; height: 3rem; color: #2563eb; }
        h1 {
            font-size: 2.25rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 1rem;
        }
        h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 1rem;
        }
        .description {
            color: #6b7280;
            margin-bottom: 2rem;
            line-height: 1.5;
        }
        .status-box {
            background: white;
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: 1px solid #f3f4f6;
            margin-bottom: 2rem;
        }
        .status-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: #4b5563;
        }
        .spinner {
            width: 1.25rem;
            height: 1.25rem;
            animation: spin 1s linear infinite;
            color: #2563eb;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .contact { font-size: 0.875rem; color: #6b7280; }
        .contact a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }
        .contact a:hover { color: #1d4ed8; }
        .footer {
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid #e5e7eb;
        }
        .footer p { font-size: 0.875rem; color: #9ca3af; }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-wrapper">
            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <h1>Even geduld...</h1>
        <h2>We zijn bezig met onderhoud</h2>
        <p class="description">
            We werken hard om {{ config('app.name', 'QuickFactuur') }} nog beter te maken. We zijn zo snel mogelijk weer online. Bedankt voor je geduld!
        </p>

        <div class="status-box">
            <div class="status-content">
                <svg class="spinner" fill="none" viewBox="0 0 24 24">
                    <circle style="opacity:0.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path style="opacity:0.75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Onderhoud bezig...</span>
            </div>
        </div>

        <div class="contact">
            <p>Heb je dringende vragen?</p>
            <a href="mailto:support@quickfactuur.nl">support@quickfactuur.nl</a>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'QuickFactuur') }}</p>
        </div>
    </div>
</body>
</html>
