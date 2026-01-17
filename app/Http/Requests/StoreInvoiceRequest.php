<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Logo and branding
            'logo' => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
            'brand_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',

            // Company fields
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|max:255',
            'company_address' => 'required|string|max:255',
            'company_phone' => 'nullable|string|max:50',
            'company_kvk' => 'nullable|string|max:20',
            // Dutch BTW number format: NL + 9 digits + B + 2 digits (e.g., NL123456789B01)
            'company_btw' => ['nullable', 'string', 'max:20', 'regex:/^(NL\d{9}B\d{2})?$/i'],
            'company_iban' => 'nullable|string|max:50',

            // Customer fields
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_address' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'customer_vat' => 'nullable|string|max:50',

            // Invoice fields
            'invoice_date' => 'required|date',
            'payment_terms' => 'required|string|in:14,30,60,direct',
            'vat_rate' => 'required|integer|in:0,9,21',
            'notes' => 'nullable|string|max:2000',

            // Items
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:500',
            'items.*.rate' => 'required|numeric|min:0|max:999999.99',
            'items.*.quantity' => 'required|integer|min:1|max:10000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'logo.max' => 'Het logo mag maximaal 2MB zijn.',
            'logo.mimes' => 'Het logo moet een PNG of JPG bestand zijn.',
            'items.required' => 'Voeg minimaal één factuurregel toe.',
            'items.*.description.required' => 'Vul een beschrijving in voor elke regel.',
            'items.*.rate.required' => 'Vul een tarief in voor elke regel.',
            'items.*.quantity.required' => 'Vul een aantal in voor elke regel.',
            'company_btw.regex' => 'Het BTW-nummer moet het formaat NL123456789B01 hebben.',
        ];
    }
}
