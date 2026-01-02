<section>
    <form method="post" action="{{ route('profile.company.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        <div>
            <label for="company_name" class="block text-sm font-medium text-slate-700 mb-2">Bedrijfsnaam</label>
            <input type="text" id="company_name" name="company_name" value="{{ old('company_name', $user->company_name) }}"
                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                placeholder="Bijv. Jansen & Zonen B.V.">
            @error('company_name')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="company_address" class="block text-sm font-medium text-slate-700 mb-2">Adres</label>
            <input type="text" id="company_address" name="company_address" value="{{ old('company_address', $user->company_address) }}"
                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                placeholder="Bijv. Hoofdstraat 1, 1234 AB Amsterdam">
            @error('company_address')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="company_phone" class="block text-sm font-medium text-slate-700 mb-2">Telefoonnummer</label>
            <input type="text" id="company_phone" name="company_phone" value="{{ old('company_phone', $user->company_phone) }}"
                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                placeholder="Bijv. 020-1234567">
            @error('company_phone')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="company_kvk" class="block text-sm font-medium text-slate-700 mb-2">KvK-nummer</label>
            <input type="text" id="company_kvk" name="company_kvk" value="{{ old('company_kvk', $user->company_kvk) }}"
                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                placeholder="Bijv. 12345678">
            @error('company_kvk')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="company_iban" class="block text-sm font-medium text-slate-700 mb-2">IBAN</label>
            <input type="text" id="company_iban" name="company_iban" value="{{ old('company_iban', $user->company_iban) }}"
                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                placeholder="Bijv. NL01BANK0123456789">
            @error('company_iban')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Divider -->
        <div class="border-t border-slate-200 pt-5 mt-5">
            <h3 class="text-sm font-semibold text-slate-900 mb-4">Facturatie Instellingen</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="invoice_prefix" class="block text-sm font-medium text-slate-700 mb-2">Factuur Prefix</label>
                <input type="text" id="invoice_prefix" name="invoice_prefix" value="{{ old('invoice_prefix', $user->invoice_prefix ?? 'FAC') }}"
                    class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors uppercase"
                    placeholder="FAC" maxlength="10">
                <p class="mt-1 text-xs text-slate-500">Bijv. FAC geeft FAC0001, FAC0002, etc.</p>
                @error('invoice_prefix')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="quote_prefix" class="block text-sm font-medium text-slate-700 mb-2">Offerte Prefix</label>
                <input type="text" id="quote_prefix" name="quote_prefix" value="{{ old('quote_prefix', $user->quote_prefix ?? 'OFF') }}"
                    class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors uppercase"
                    placeholder="OFF" maxlength="10">
                <p class="mt-1 text-xs text-slate-500">Bijv. OFF geeft OFF0001, OFF0002, etc.</p>
                @error('quote_prefix')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="default_payment_terms" class="block text-sm font-medium text-slate-700 mb-2">Standaard Betalingstermijn</label>
            <select id="default_payment_terms" name="default_payment_terms"
                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                <option value="direct" {{ old('default_payment_terms', $user->default_payment_terms) === 'direct' ? 'selected' : '' }}>Direct</option>
                <option value="14" {{ old('default_payment_terms', $user->default_payment_terms) === '14' ? 'selected' : '' }}>14 dagen</option>
                <option value="30" {{ old('default_payment_terms', $user->default_payment_terms ?? '30') === '30' ? 'selected' : '' }}>30 dagen</option>
                <option value="60" {{ old('default_payment_terms', $user->default_payment_terms) === '60' ? 'selected' : '' }}>60 dagen</option>
            </select>
            @error('default_payment_terms')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-4 pt-4">
            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-xl shadow-lg shadow-blue-200 hover:shadow-xl transition-all duration-200">
                Opslaan
            </button>

            @if (session('status') === 'company-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-emerald-600 font-medium"
                >Opgeslagen!</p>
            @endif
        </div>
    </form>
</section>
