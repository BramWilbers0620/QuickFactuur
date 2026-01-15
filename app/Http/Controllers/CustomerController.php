<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Display a listing of the customers.
     */
    public function index(Request $request): View
    {
        $query = Customer::where('user_id', auth()->id());

        // Search by name or email
        if ($search = $request->input('search')) {
            $escapedSearch = str_replace(['%', '_'], ['\\%', '\\_'], $search);
            $query->where(function ($q) use ($escapedSearch) {
                $q->where('name', 'like', "%{$escapedSearch}%")
                  ->orWhere('email', 'like', "%{$escapedSearch}%");
            });
        }

        $customers = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create(): View
    {
        return view('customers.create');
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'vat_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['user_id'] = auth()->id();

        Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Klant succesvol aangemaakt.');
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer): View|RedirectResponse
    {
        $this->authorize('update', $customer);

        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $this->authorize('update', $customer);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'vat_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Klant succesvol bijgewerkt.');
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        $this->authorize('delete', $customer);

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Klant succesvol verwijderd.');
    }

    /**
     * Get customer data as JSON (for AJAX requests).
     */
    public function show(Customer $customer)
    {
        $this->authorize('view', $customer);

        return response()->json($customer);
    }
}
