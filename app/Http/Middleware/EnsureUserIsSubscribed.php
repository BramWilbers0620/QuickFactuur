<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsSubscribed
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect('/login');
        }

        // Use the User model's hasActiveAccess method for consistent logic
        if ($user->hasActiveAccess()) {
            return $next($request);
        }

        // Determine the appropriate error message
        $errorMessage = 'Je hebt een actief abonnement nodig om deze pagina te bekijken.';

        if ($user->trial_ends_at && now()->gt($user->trial_ends_at)) {
            $errorMessage = 'Je gratis trial is verlopen. Kies een abonnement om door te blijven factureren.';
        }

        return redirect()->route('billing')->with('error', $errorMessage);
    }
}
