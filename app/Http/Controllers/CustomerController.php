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
    public function index(): View
    {
        $customers = Customer::where('user_id', auth()->id())
            ->orderBy('name')
            ->paginate(10);

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
        // Ensure user owns this customer
        if ($customer->user_id !== auth()->id()) {
            abort(403);
        }

        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, Customer $customer): RedirectResponse
    {
        // Ensure user owns this customer
        if ($customer->user_id !== auth()->id()) {
            abort(403);
        }

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
        // Ensure user owns this customer
        if ($customer->user_id !== auth()->id()) {
            abort(403);
        }

        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Klant succesvol verwijderd.');
    }

    /**
     * Get customer data as JSON (for AJAX requests).
     */
    public function show(Customer $customer)
    {
        // Ensure user owns this customer
        if ($customer->user_id !== auth()->id()) {
            abort(403);
        }

        return response()->json($customer);
    }
}
