<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-slate-600 hover:text-blue-600 transition-colors duration-200 group">
                    <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Terug naar dashboard
                </a>
            </div>

            @if($errors->any())
                <div class="mb-6">
                    <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Main Card -->
            <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-emerald-600 to-green-600 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mr-5">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-white">Nieuwe Offerte</h1>
                                <p class="text-emerald-100 text-sm">Maak een professionele offerte voor je klant</p>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('quotes.generate') }}" method="POST" class="p-8" enctype="multipart/form-data">
                    @csrf

                    <!-- Logo & Kleur sectie -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 p-6 bg-slate-50 rounded-2xl border border-slate-200">
                        <!-- Logo Upload -->
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-2">Bedrijfslogo</label>
                            <div class="flex items-center gap-4">
                                <div id="logo-preview" class="w-20 h-20 bg-white border-2 border-dashed border-slate-300 rounded-xl flex items-center justify-center overflow-hidden">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <input type="file" id="logo" name="logo" accept="image/png,image/jpeg,image/jpg"
                                           class="hidden" onchange="previewLogo(this)">
                                    <label for="logo" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 cursor-pointer transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                        </svg>
                                        Upload logo
                                    </label>
                                    <p class="text-xs text-slate-400 mt-1">PNG of JPG, max 2MB</p>
                                </div>
                            </div>
                        </div>

                        <!-- Kleurkeuze -->
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-2">Huiskleur</label>
                            <div id="color-picker" class="flex flex-wrap gap-3">
                                <label class="color-option cursor-pointer">
                                    <input type="radio" name="brand_color" value="#2563eb" class="hidden" checked>
                                    <div class="color-box w-12 h-12 rounded-xl flex items-center justify-center border-4 border-slate-800 scale-110 transition-all shadow-md hover:scale-105" style="background-color: #2563eb;">
                                        <svg class="color-check w-6 h-6 text-white drop-shadow" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </label>
                                <label class="color-option cursor-pointer">
                                    <input type="radio" name="brand_color" value="#059669" class="hidden">
                                    <div class="color-box w-12 h-12 rounded-xl flex items-center justify-center border-4 border-transparent transition-all shadow-md hover:scale-105" style="background-color: #059669;">
                                        <svg class="color-check w-6 h-6 text-white drop-shadow hidden" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </label>
                                <label class="color-option cursor-pointer">
                                    <input type="radio" name="brand_color" value="#7c3aed" class="hidden">
                                    <div class="color-box w-12 h-12 rounded-xl flex items-center justify-center border-4 border-transparent transition-all shadow-md hover:scale-105" style="background-color: #7c3aed;">
                                        <svg class="color-check w-6 h-6 text-white drop-shadow hidden" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </label>
                                <label class="color-option cursor-pointer">
                                    <input type="radio" name="brand_color" value="#dc2626" class="hidden">
                                    <div class="color-box w-12 h-12 rounded-xl flex items-center justify-center border-4 border-transparent transition-all shadow-md hover:scale-105" style="background-color: #dc2626;">
                                        <svg class="color-check w-6 h-6 text-white drop-shadow hidden" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </label>
                                <label class="color-option cursor-pointer">
                                    <input type="radio" name="brand_color" value="#d97706" class="hidden">
                                    <div class="color-box w-12 h-12 rounded-xl flex items-center justify-center border-4 border-transparent transition-all shadow-md hover:scale-105" style="background-color: #d97706;">
                                        <svg class="color-check w-6 h-6 text-white drop-shadow hidden" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </label>
                                <label class="color-option cursor-pointer">
                                    <input type="radio" name="brand_color" value="#0891b2" class="hidden">
                                    <div class="color-box w-12 h-12 rounded-xl flex items-center justify-center border-4 border-transparent transition-all shadow-md hover:scale-105" style="background-color: #0891b2;">
                                        <svg class="color-check w-6 h-6 text-white drop-shadow hidden" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </label>
                                <label class="color-option cursor-pointer">
                                    <input type="radio" name="brand_color" value="#4b5563" class="hidden">
                                    <div class="color-box w-12 h-12 rounded-xl flex items-center justify-center border-4 border-transparent transition-all shadow-md hover:scale-105" style="background-color: #4b5563;">
                                        <svg class="color-check w-6 h-6 text-white drop-shadow hidden" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </label>
                                <label class="color-option cursor-pointer">
                                    <input type="radio" name="brand_color" value="#1f2937" class="hidden">
                                    <div class="color-box w-12 h-12 rounded-xl flex items-center justify-center border-4 border-transparent transition-all shadow-md hover:scale-105" style="background-color: #1f2937;">
                                        <svg class="color-check w-6 h-6 text-white drop-shadow hidden" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </label>
                            </div>
                            <p class="text-xs text-slate-400 mt-2">Wordt gebruikt voor titels en accenten in de PDF</p>
                        </div>
                    </div>

                    <!-- Two Column Layout: Van & Rekening naar -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                        <!-- Van (Bedrijfsgegevens) -->
                        <div class="space-y-5">
                            <div class="flex items-center mb-4">
                                <div class="w-9 h-9 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <h2 class="text-lg font-bold text-slate-900">Van</h2>
                            </div>

                            <div>
                                <label for="company_name" class="block text-sm font-medium text-slate-600 mb-1.5">Bedrijfsnaam *</label>
                                <input type="text" id="company_name" name="company_name" value="{{ old('company_name', $companyProfile['name'] ?? '') }}"
                                       class="w-full border border-slate-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('company_name') border-red-400 bg-red-50 @enderror"
                                       placeholder="Jouw bedrijfsnaam" required>
                            </div>

                            <div>
                                <label for="company_email" class="block text-sm font-medium text-slate-600 mb-1.5">E-mail *</label>
                                <input type="email" id="company_email" name="company_email" value="{{ old('company_email', $companyProfile['email'] ?? '') }}"
                                       class="w-full border border-slate-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('company_email') border-red-400 bg-red-50 @enderror"
                                       placeholder="jouw@email.nl" required>
                            </div>

                            <div>
                                <label for="company_address" class="block text-sm font-medium text-slate-600 mb-1.5">Adres *</label>
                                <input type="text" id="company_address" name="company_address" value="{{ old('company_address', $companyProfile['address'] ?? '') }}"
                                       class="w-full border border-slate-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('company_address') border-red-400 bg-red-50 @enderror"
                                       placeholder="Straat 123, 1234 AB Stad" required>
                            </div>

                            <div>
                                <label for="company_phone" class="block text-sm font-medium text-slate-600 mb-1.5">Telefoon</label>
                                <input type="tel" id="company_phone" name="company_phone" value="{{ old('company_phone', $companyProfile['phone'] ?? '') }}"
                                       class="w-full border border-slate-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                       placeholder="06 12345678">
                            </div>

                            <div>
                                <label for="company_kvk" class="block text-sm font-medium text-slate-600 mb-1.5">KVK-nummer</label>
                                <input type="text" id="company_kvk" name="company_kvk" value="{{ old('company_kvk', $companyProfile['kvk'] ?? '') }}"
                                       class="w-full border border-slate-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                       placeholder="12345678">
                            </div>
                        </div>

                        <!-- Rekening naar (Klantgegevens) -->
                        <div class="space-y-5">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <div class="w-9 h-9 bg-gradient-to-br from-emerald-100 to-green-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <h2 class="text-lg font-bold text-slate-900">Rekening naar</h2>
                                </div>
                                <a href="{{ route('customers.create') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                    + Nieuwe klant
                                </a>
                            </div>

                            @if(isset($customers) && $customers->count() > 0)
                            <div>
                                <label for="customer_select" class="block text-sm font-medium text-slate-600 mb-1.5">Kies bestaande klant</label>
                                <select id="customer_select" onchange="fillCustomerData(this)"
                                        class="w-full border border-slate-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white">
                                    <option value="">-- Selecteer een klant of vul handmatig in --</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}"
                                                data-name="{{ $customer->name }}"
                                                data-email="{{ $customer->email }}"
                                                data-address="{{ $customer->address }}"
                                                data-phone="{{ $customer->phone }}">
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <div>
                                <label for="customer_name" class="block text-sm font-medium text-slate-600 mb-1.5">Klantnaam *</label>
                                <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}"
                                       class="w-full border border-slate-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('customer_name') border-red-400 bg-red-50 @enderror"
                                       placeholder="Naam klant of bedrijf" required>
                            </div>

                            <div>
                                <label for="customer_email" class="block text-sm font-medium text-slate-600 mb-1.5">E-mail</label>
                                <input type="email" id="customer_email" name="customer_email" value="{{ old('customer_email') }}"
                                       class="w-full border border-slate-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                       placeholder="klant@email.nl">
                            </div>

                            <div>
                                <label for="customer_address" class="block text-sm font-medium text-slate-600 mb-1.5">Adres</label>
                                <input type="text" id="customer_address" name="customer_address" value="{{ old('customer_address') }}"
                                       class="w-full border border-slate-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                       placeholder="Straat 123, 1234 AB Stad">
                            </div>

                            <div>
                                <label for="customer_phone" class="block text-sm font-medium text-slate-600 mb-1.5">Telefoon</label>
                                <input type="tel" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}"
                                       class="w-full border border-slate-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                       placeholder="06 12345678">
                            </div>

                        </div>
                    </div>

                    <div class="border-t border-slate-100 my-8"></div>

                    <!-- Offerte Info -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div>
                            <label for="quote_number" class="block text-sm font-medium text-slate-600 mb-1.5">Offertenummer</label>
                            <input type="text" id="quote_number" name="quote_number" value="{{ old('quote_number', $nextQuoteNumber ?? '') }}"
                                   class="w-full border border-slate-200 rounded-lg px-4 py-2.5 bg-slate-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                   placeholder="OFF0001" readonly>
                            <p class="text-xs text-slate-400 mt-1">Automatisch gegenereerd</p>
                        </div>

                        <div>
                            <label for="quote_date" class="block text-sm font-medium text-slate-600 mb-1.5">Offertedatum *</label>
                            <input type="date" id="quote_date" name="quote_date" value="{{ old('quote_date', date('Y-m-d')) }}"
                                   class="w-full border border-slate-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all" required>
                        </div>

                        <div>
                            <label for="valid_days" class="block text-sm font-medium text-slate-600 mb-1.5">Geldig voor</label>
                            <select id="valid_days" name="valid_days"
                                    class="w-full border border-slate-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all bg-white">
                                <option value="14" {{ old('valid_days') == '14' ? 'selected' : '' }}>14 dagen</option>
                                <option value="30" {{ old('valid_days', '30') == '30' ? 'selected' : '' }}>30 dagen</option>
                                <option value="60" {{ old('valid_days') == '60' ? 'selected' : '' }}>60 dagen</option>
                                <option value="90" {{ old('valid_days') == '90' ? 'selected' : '' }}>90 dagen</option>
                            </select>
                        </div>

                        <div>
                            <label for="vat_rate" class="block text-sm font-medium text-slate-600 mb-1.5">BTW-tarief</label>
                            <select id="vat_rate" name="vat_rate"
                                    class="w-full border border-slate-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white">
                                <option value="21" {{ old('vat_rate', '21') == '21' ? 'selected' : '' }}>21% (standaard)</option>
                                <option value="9" {{ old('vat_rate') == '9' ? 'selected' : '' }}>9% (verlaagd)</option>
                                <option value="0" {{ old('vat_rate') == '0' ? 'selected' : '' }}>0% (vrijgesteld)</option>
                            </select>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 my-8"></div>

                    <!-- Offerteregels -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="w-9 h-9 bg-gradient-to-br from-emerald-100 to-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <h2 class="text-lg font-bold text-slate-900">Offerteregels</h2>
                            </div>
                            <button type="button" id="add-row-btn"
                                    class="inline-flex items-center px-4 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-600 font-medium rounded-lg transition-colors text-sm">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Regel toevoegen
                            </button>
                        </div>

                        <!-- Table Header -->
                        <div class="hidden md:grid md:grid-cols-12 gap-4 px-4 py-3 bg-slate-50 rounded-t-xl border border-slate-200 border-b-0">
                            <div class="col-span-5 text-xs font-semibold text-slate-500 uppercase tracking-wider">Beschrijving</div>
                            <div class="col-span-2 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Prijs</div>
                            <div class="col-span-2 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Aantal</div>
                            <div class="col-span-2 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Totaal</div>
                            <div class="col-span-1"></div>
                        </div>

                        <!-- Invoice Lines Container -->
                        <div id="invoice-lines" class="border border-slate-200 rounded-b-xl md:rounded-t-none rounded-xl divide-y divide-slate-100">
                            <!-- Default first row -->
                            <div class="invoice-line grid grid-cols-1 md:grid-cols-12 gap-4 p-4 items-center">
                                <div class="md:col-span-5">
                                    <label class="md:hidden text-xs font-medium text-slate-500 mb-1 block">Beschrijving</label>
                                    <input type="text" name="items[0][description]" placeholder="Product of dienst beschrijving"
                                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="md:hidden text-xs font-medium text-slate-500 mb-1 block">Prijs</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm leading-none">€</span>
                                        <input type="number" name="items[0][rate]" step="0.01" min="0" placeholder="0.00"
                                               class="item-rate w-full border border-slate-200 rounded-lg pl-8 pr-3 py-2 text-sm text-right focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                    </div>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="md:hidden text-xs font-medium text-slate-500 mb-1 block">Aantal</label>
                                    <input type="number" name="items[0][quantity]" min="1" value="1" placeholder="1"
                                           class="item-quantity w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-right focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="md:hidden text-xs font-medium text-slate-500 mb-1 block">Totaal</label>
                                    <div class="item-total text-right font-semibold text-slate-700 py-2">€ 0,00</div>
                                </div>
                                <div class="md:col-span-1 flex justify-end">
                                    <button type="button" class="remove-row-btn p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors opacity-50 cursor-not-allowed" disabled>
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Totalen -->
                    <div class="flex justify-end mb-8">
                        <div class="w-full md:w-80 space-y-3">
                            <div class="flex justify-between items-center py-2 px-4 bg-slate-50 rounded-lg">
                                <span class="text-slate-600">Subtotaal</span>
                                <span id="subtotal-display" class="font-semibold text-slate-900">€ 0,00</span>
                            </div>
                            <div class="flex justify-between items-center py-2 px-4 bg-slate-50 rounded-lg">
                                <span class="text-slate-600">BTW (<span id="vat-percentage-display">21</span>%)</span>
                                <span id="vat-display" class="font-semibold text-slate-900">€ 0,00</span>
                            </div>
                            <div class="flex justify-between items-center py-3 px-4 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-xl text-white">
                                <span class="font-semibold">Totaal</span>
                                <span id="total-display" class="text-xl font-bold">€ 0,00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Opmerkingen -->
                    <div class="mb-8">
                        <label for="notes" class="block text-sm font-medium text-slate-600 mb-1.5">Opmerkingen / Notities</label>
                        <textarea id="notes" name="notes" rows="3"
                                  class="w-full border border-slate-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all resize-none"
                                  placeholder="Eventuele extra opmerkingen voor de offerte...">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-slate-100">
                        <button type="submit" id="submit-btn"
                                class="flex-1 bg-white hover:bg-slate-200 text-slate-800 font-semibold py-4 px-6 rounded-xl transition-all duration-200 shadow-lg shadow-slate-200 hover:shadow-xl border-2 border-emerald-600 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center group">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-slate-800 hidden" id="submit-spinner" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <svg class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform" id="submit-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                            <span id="submit-text">Genereer PDF Offerte</span>
                        </button>
                        <a href="{{ route('dashboard') }}"
                           class="sm:w-auto px-8 py-4 border-2 border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold rounded-xl transition-all duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Annuleren
                        </a>
                    </div>
                </form>
            </div>

            <!-- Help Section -->
            <div class="mt-8 bg-white/60 backdrop-blur-sm rounded-2xl border border-slate-200/50 p-6">
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-bold text-slate-900 mb-2">Tips voor je offerte</h3>
                        <ul class="text-sm text-slate-600 space-y-1.5">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-emerald-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Voeg meerdere regels toe voor verschillende producten/diensten
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-emerald-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                Geaccepteerde offertes kun je met één klik omzetten naar een factuur
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 text-emerald-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                De PDF wordt direct gedownload na het genereren
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let rowIndex = 1;

        function getVatRate() {
            const vatSelect = document.getElementById('vat_rate');
            return parseFloat(vatSelect.value) / 100;
        }

        function formatCurrency(amount) {
            return '€ ' + amount.toLocaleString('nl-NL', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function updateRowTotal(row) {
            const rate = parseFloat(row.querySelector('.item-rate').value) || 0;
            const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
            const total = rate * quantity;
            row.querySelector('.item-total').textContent = formatCurrency(total);
        }

        function updateTotals() {
            let subtotal = 0;
            document.querySelectorAll('.invoice-line').forEach(row => {
                const rate = parseFloat(row.querySelector('.item-rate').value) || 0;
                const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
                subtotal += rate * quantity;
            });

            const vatRate = getVatRate();
            const vatPercentage = document.getElementById('vat_rate').value;
            const vat = subtotal * vatRate;
            const total = subtotal + vat;

            document.getElementById('subtotal-display').textContent = formatCurrency(subtotal);
            document.getElementById('vat-percentage-display').textContent = vatPercentage;
            document.getElementById('vat-display').textContent = formatCurrency(vat);
            document.getElementById('total-display').textContent = formatCurrency(total);
        }

        function updateRemoveButtons() {
            const rows = document.querySelectorAll('.invoice-line');
            rows.forEach((row, index) => {
                const btn = row.querySelector('.remove-row-btn');
                if (rows.length === 1) {
                    btn.disabled = true;
                    btn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    btn.disabled = false;
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            });
        }

        function addRow() {
            const container = document.getElementById('invoice-lines');
            const newRow = document.createElement('div');
            newRow.className = 'invoice-line grid grid-cols-1 md:grid-cols-12 gap-4 p-4 items-center';
            newRow.innerHTML = `
                <div class="md:col-span-5">
                    <label class="md:hidden text-xs font-medium text-slate-500 mb-1 block">Beschrijving</label>
                    <input type="text" name="items[${rowIndex}][description]" placeholder="Product of dienst beschrijving"
                           class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div class="md:col-span-2">
                    <label class="md:hidden text-xs font-medium text-slate-500 mb-1 block">Prijs</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm leading-none">€</span>
                        <input type="number" name="items[${rowIndex}][rate]" step="0.01" min="0" placeholder="0.00"
                               class="item-rate w-full border border-slate-200 rounded-lg pl-8 pr-3 py-2 text-sm text-right focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="md:hidden text-xs font-medium text-slate-500 mb-1 block">Aantal</label>
                    <input type="number" name="items[${rowIndex}][quantity]" min="1" value="1" placeholder="1"
                           class="item-quantity w-full border border-slate-200 rounded-lg px-3 py-2 text-sm text-right focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div class="md:col-span-2">
                    <label class="md:hidden text-xs font-medium text-slate-500 mb-1 block">Totaal</label>
                    <div class="item-total text-right font-semibold text-slate-700 py-2">€ 0,00</div>
                </div>
                <div class="md:col-span-1 flex justify-end">
                    <button type="button" class="remove-row-btn p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>
            `;

            container.appendChild(newRow);
            rowIndex++;

            // Add event listeners to new row
            const rateInput = newRow.querySelector('.item-rate');
            const quantityInput = newRow.querySelector('.item-quantity');
            const removeBtn = newRow.querySelector('.remove-row-btn');

            rateInput.addEventListener('input', () => {
                updateRowTotal(newRow);
                updateTotals();
            });

            quantityInput.addEventListener('input', () => {
                updateRowTotal(newRow);
                updateTotals();
            });

            removeBtn.addEventListener('click', () => {
                newRow.remove();
                updateTotals();
                updateRemoveButtons();
            });

            updateRemoveButtons();
        }

        // Initialize event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Add row button
            document.getElementById('add-row-btn').addEventListener('click', addRow);

            // VAT rate change listener
            document.getElementById('vat_rate').addEventListener('change', updateTotals);

            // Initial row event listeners
            document.querySelectorAll('.invoice-line').forEach(row => {
                const rateInput = row.querySelector('.item-rate');
                const quantityInput = row.querySelector('.item-quantity');

                rateInput.addEventListener('input', () => {
                    updateRowTotal(row);
                    updateTotals();
                });

                quantityInput.addEventListener('input', () => {
                    updateRowTotal(row);
                    updateTotals();
                });
            });

            // Form submit loading state
            document.querySelector('form').addEventListener('submit', function(e) {
                const btn = document.getElementById('submit-btn');
                const spinner = document.getElementById('submit-spinner');
                const icon = document.getElementById('submit-icon');
                const text = document.getElementById('submit-text');

                if (!this.checkValidity()) {
                    return;
                }

                btn.disabled = true;
                spinner.classList.remove('hidden');
                icon.classList.add('hidden');
                text.textContent = 'Offerte wordt gegenereerd...';
            });

            updateTotals();
            initColorPicker();
        });

        // Color picker
        function initColorPicker() {
            const colorOptions = document.querySelectorAll('#color-picker .color-option');

            colorOptions.forEach(option => {
                const input = option.querySelector('input');
                const box = option.querySelector('.color-box');
                const check = option.querySelector('.color-check');

                input.addEventListener('change', function() {
                    // Reset all
                    colorOptions.forEach(opt => {
                        const b = opt.querySelector('.color-box');
                        const c = opt.querySelector('.color-check');
                        b.classList.remove('border-slate-800', 'scale-110');
                        b.classList.add('border-transparent');
                        c.classList.add('hidden');
                    });

                    // Activate selected
                    box.classList.remove('border-transparent');
                    box.classList.add('border-slate-800', 'scale-110');
                    check.classList.remove('hidden');
                });
            });
        }

        // Logo preview
        function previewLogo(input) {
            const preview = document.getElementById('logo-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-contain">`;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Fill customer data from dropdown
        function fillCustomerData(select) {
            const option = select.options[select.selectedIndex];

            if (option.value) {
                document.getElementById('customer_name').value = option.dataset.name || '';
                document.getElementById('customer_email').value = option.dataset.email || '';
                document.getElementById('customer_address').value = option.dataset.address || '';
                document.getElementById('customer_phone').value = option.dataset.phone || '';
            } else {
                // Clear fields if "select" option is chosen
                document.getElementById('customer_name').value = '';
                document.getElementById('customer_email').value = '';
                document.getElementById('customer_address').value = '';
                document.getElementById('customer_phone').value = '';
            }
        }
    </script>
</x-app-layout>
