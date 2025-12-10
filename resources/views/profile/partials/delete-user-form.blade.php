<section class="space-y-5">
    <div class="p-4 bg-red-50 border border-red-200 rounded-xl">
        <p class="text-sm text-red-700">
            <strong>Let op:</strong> Als je je account verwijdert, worden al je gegevens permanent verwijderd. Dit omvat al je facturen, klanten en bedrijfsgegevens. Deze actie kan niet ongedaan worden gemaakt.
        </p>
    </div>

    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="px-6 py-3 bg-gradient-to-r from-red-500 to-rose-500 hover:from-red-600 hover:to-rose-600 text-white font-semibold rounded-xl shadow-lg shadow-red-200 hover:shadow-xl transition-all duration-200"
    >
        Account Verwijderen
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-slate-900">
                    Account Verwijderen
                </h2>
            </div>

            <p class="text-slate-600 mb-6">
                Weet je zeker dat je je account wilt verwijderen? Al je gegevens worden permanent verwijderd. Voer je wachtwoord in om te bevestigen.
            </p>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Wachtwoord</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                    placeholder="Voer je wachtwoord in"
                />
                @if ($errors->userDeletion->get('password'))
                    <p class="mt-2 text-sm text-red-600">{{ $errors->userDeletion->first('password') }}</p>
                @endif
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close')"
                    class="px-5 py-2.5 text-slate-700 hover:text-slate-900 font-medium transition-colors">
                    Annuleren
                </button>

                <button type="submit"
                    class="px-5 py-2.5 bg-gradient-to-r from-red-500 to-rose-500 hover:from-red-600 hover:to-rose-600 text-white font-semibold rounded-xl transition-all duration-200">
                    Ja, Verwijder Mijn Account
                </button>
            </div>
        </form>
    </x-modal>
</section>
