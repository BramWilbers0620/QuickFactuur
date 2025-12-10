<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\TrialStartedMail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Automatisch 7 dagen gratis trial bij registratie
        $trialDays = config('quickfactuur.trial_days', 7);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'trial_ends_at' => now()->addDays($trialDays),
        ]);

        event(new Registered($user));

        // Stuur welkomstmail met trial info
        Mail::to($user->email)->queue(new TrialStartedMail($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false))
            ->with('success', 'Welkom bij QuickFactuur! Je gratis ' . $trialDays . '-dagen trial is gestart.');
    }
}
