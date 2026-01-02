<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StripeWebhookController;

// Homepagina
Route::get('/', function () {
    return view('welcome');
});

// Dashboard - alleen bereikbaar voor geverifieerde én betalende gebruikers
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'subscribed'])
    ->name('dashboard');

// Stripe Webhook (geen CSRF, geen auth)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])->name('stripe.webhook');

// Abonnementen (alleen ingelogd nodig, met rate limiting)
Route::middleware(['auth', 'throttle:10,1'])->group(function () {
    Route::get('/abonnement', [BillingController::class, 'index'])->name('billing');
    Route::post('/abonnement/start', [BillingController::class, 'subscribe'])->name('subscribe');
    Route::get('/abonnement/success', [BillingController::class, 'success'])->name('billing.success');
    Route::post('/abonnement/opzeggen', [BillingController::class, 'cancel'])->name('subscription.cancel');
});

// Facturen aanmaken — alleen mogelijk voor betalende gebruikers
Route::middleware(['auth', 'subscribed'])->group(function () {
    Route::get('/facturen', [InvoiceController::class, 'index'])->name('invoice.index');
    Route::get('/facturen/nieuw', [InvoiceController::class, 'create'])->name('invoice.create');
    Route::get('/facturen/{invoice}', [InvoiceController::class, 'show'])->name('invoice.show');
    Route::get('/facturen/{invoice}/download', [InvoiceController::class, 'download'])
        ->middleware('throttle:30,1')
        ->name('invoice.download');

    // Rate limited routes (prevent abuse)
    Route::post('/facturen/genereren', [InvoiceController::class, 'generate'])
        ->middleware('throttle:20,1')
        ->name('invoice.generate');
    Route::patch('/facturen/{invoice}/status', [InvoiceController::class, 'updateStatus'])
        ->middleware('throttle:30,1')
        ->name('invoice.status');
    Route::post('/facturen/{invoice}/email', [InvoiceController::class, 'sendEmail'])
        ->middleware('throttle:10,1')
        ->name('invoice.email');
});

// Offertes — alleen mogelijk voor betalende gebruikers
Route::middleware(['auth', 'subscribed'])->group(function () {
    Route::get('/offertes', [QuoteController::class, 'index'])->name('quotes.index');
    Route::get('/offertes/nieuw', [QuoteController::class, 'create'])->name('quotes.create');
    Route::get('/offertes/{quote}/download', [QuoteController::class, 'download'])
        ->middleware('throttle:30,1')
        ->name('quotes.download');

    // Rate limited routes (prevent abuse)
    Route::post('/offertes/genereren', [QuoteController::class, 'generate'])
        ->middleware('throttle:20,1')
        ->name('quotes.generate');
    Route::post('/offertes/{quote}/omzetten', [QuoteController::class, 'convertToInvoice'])
        ->middleware('throttle:20,1')
        ->name('quotes.convert');
    Route::patch('/offertes/{quote}/status', [QuoteController::class, 'updateStatus'])
        ->middleware('throttle:30,1')
        ->name('quotes.status');
});

// Klantenbeheer — alleen mogelijk voor betalende gebruikers
Route::middleware(['auth', 'subscribed'])->group(function () {
    Route::get('/klanten', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/klanten/nieuw', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/klanten', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/klanten/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('/klanten/{customer}/bewerken', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::patch('/klanten/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/klanten/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
});

// Profielbeheer
Route::middleware(['auth'])->group(function () {
    Route::get('/profiel', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profiel', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profiel/bedrijf', [ProfileController::class, 'updateCompany'])->name('profile.company.update');
    Route::delete('/profiel', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Auth routes (login, register, etc.)
require __DIR__.'/auth.php';
