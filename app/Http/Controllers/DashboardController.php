<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Quote;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userId = $user->id;

        // Calculate statistics only if user has access
        $stats = null;
        if ($user->hasActiveAccess()) {
            // Cache statistics for 5 minutes per user
            $stats = Cache::remember(
                "dashboard_stats_{$userId}",
                now()->addMinutes(5),
                fn() => $this->calculateStats($userId)
            );
        }

        return view('dashboard', compact('stats'));
    }

    /**
     * Clear cached dashboard stats for a user.
     * Call this when invoices/quotes are created/updated.
     */
    public static function clearStatsCache(int $userId): void
    {
        Cache::forget("dashboard_stats_{$userId}");
    }

    private function calculateStats(int $userId): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfYear = $now->copy()->startOfYear();

        // Invoice statistics
        $totalInvoices = Invoice::where('user_id', $userId)->count();
        $invoicesThisMonth = Invoice::where('user_id', $userId)
            ->where('invoice_date', '>=', $startOfMonth)
            ->count();

        $totalRevenue = Invoice::where('user_id', $userId)->sum('total');
        $revenueThisMonth = Invoice::where('user_id', $userId)
            ->where('invoice_date', '>=', $startOfMonth)
            ->sum('total');
        $revenueThisYear = Invoice::where('user_id', $userId)
            ->where('invoice_date', '>=', $startOfYear)
            ->sum('total');

        // Invoice status counts
        $paidInvoices = Invoice::where('user_id', $userId)->where('status', 'betaald')->count();
        $pendingInvoices = Invoice::where('user_id', $userId)->whereIn('status', ['concept', 'verzonden'])->count();
        $overdueInvoices = Invoice::where('user_id', $userId)
            ->where('status', '!=', 'betaald')
            ->whereNotNull('due_date')
            ->where('due_date', '<', $now)
            ->count();

        // Quote statistics
        $totalQuotes = Quote::where('user_id', $userId)->count();
        $pendingQuotes = Quote::where('user_id', $userId)
            ->whereIn('status', ['concept', 'verzonden'])
            ->count();
        $acceptedQuotes = Quote::where('user_id', $userId)
            ->where('status', 'geaccepteerd')
            ->count();

        // Customer count
        $totalCustomers = Customer::where('user_id', $userId)->count();

        // Recent invoices
        $recentInvoices = Invoice::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Monthly revenue for chart (last 6 months)
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $revenue = Invoice::where('user_id', $userId)
                ->whereYear('invoice_date', $month->year)
                ->whereMonth('invoice_date', $month->month)
                ->sum('total');
            $monthlyRevenue[] = [
                'month' => $month->format('M'),
                'revenue' => (float) $revenue,
            ];
        }

        return [
            'totalInvoices' => $totalInvoices,
            'invoicesThisMonth' => $invoicesThisMonth,
            'totalRevenue' => $totalRevenue,
            'revenueThisMonth' => $revenueThisMonth,
            'revenueThisYear' => $revenueThisYear,
            'paidInvoices' => $paidInvoices,
            'pendingInvoices' => $pendingInvoices,
            'overdueInvoices' => $overdueInvoices,
            'totalQuotes' => $totalQuotes,
            'pendingQuotes' => $pendingQuotes,
            'acceptedQuotes' => $acceptedQuotes,
            'totalCustomers' => $totalCustomers,
            'recentInvoices' => $recentInvoices,
            'monthlyRevenue' => $monthlyRevenue,
        ];
    }
}
