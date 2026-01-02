<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Quote;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
        $sixMonthsAgo = $now->copy()->subMonths(5)->startOfMonth();

        // Single optimized query for all invoice statistics
        $invoiceStats = Invoice::where('user_id', $userId)
            ->selectRaw('
                COUNT(*) as total_count,
                SUM(total) as total_revenue,
                SUM(CASE WHEN invoice_date >= ? THEN 1 ELSE 0 END) as month_count,
                SUM(CASE WHEN invoice_date >= ? THEN total ELSE 0 END) as month_revenue,
                SUM(CASE WHEN invoice_date >= ? THEN total ELSE 0 END) as year_revenue,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as paid_count,
                SUM(CASE WHEN status IN (?, ?) THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN status != ? AND due_date IS NOT NULL AND due_date < ? THEN 1 ELSE 0 END) as overdue_count
            ', [$startOfMonth, $startOfMonth, $startOfYear, 'betaald', 'concept', 'verzonden', 'betaald', $now])
            ->first();

        // Single optimized query for all quote statistics
        $quoteStats = Quote::where('user_id', $userId)
            ->selectRaw('
                COUNT(*) as total_count,
                SUM(CASE WHEN status IN (?, ?) THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as accepted_count
            ', ['concept', 'verzonden', 'geaccepteerd'])
            ->first();

        // Customer count (single query)
        $totalCustomers = Customer::where('user_id', $userId)->count();

        // Recent invoices (single query)
        $recentInvoices = Invoice::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Monthly revenue for chart - single query instead of 6
        // Use database-agnostic date extraction
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            $monthlyRevenueData = Invoice::where('user_id', $userId)
                ->where('invoice_date', '>=', $sixMonthsAgo)
                ->selectRaw("strftime('%Y', invoice_date) as year, strftime('%m', invoice_date) as month, SUM(total) as revenue")
                ->groupByRaw("strftime('%Y', invoice_date), strftime('%m', invoice_date)")
                ->get()
                ->keyBy(fn($item) => $item->year . '-' . $item->month);
        } elseif ($driver === 'pgsql') {
            // PostgreSQL uses EXTRACT()
            $monthlyRevenueData = Invoice::where('user_id', $userId)
                ->where('invoice_date', '>=', $sixMonthsAgo)
                ->selectRaw("EXTRACT(YEAR FROM invoice_date)::int as year, EXTRACT(MONTH FROM invoice_date)::int as month, SUM(total) as revenue")
                ->groupByRaw("EXTRACT(YEAR FROM invoice_date), EXTRACT(MONTH FROM invoice_date)")
                ->get()
                ->keyBy(fn($item) => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT));
        } else {
            // MySQL / MariaDB
            $monthlyRevenueData = Invoice::where('user_id', $userId)
                ->where('invoice_date', '>=', $sixMonthsAgo)
                ->selectRaw('YEAR(invoice_date) as year, MONTH(invoice_date) as month, SUM(total) as revenue')
                ->groupByRaw('YEAR(invoice_date), MONTH(invoice_date)')
                ->get()
                ->keyBy(fn($item) => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT));
        }

        // Build monthly revenue array with all months (including zero months)
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $key = $month->format('Y-m');
            $monthlyRevenue[] = [
                'month' => $month->format('M'),
                'revenue' => (float) ($monthlyRevenueData->get($key)?->revenue ?? 0),
            ];
        }

        return [
            'totalInvoices' => (int) ($invoiceStats->total_count ?? 0),
            'invoicesThisMonth' => (int) ($invoiceStats->month_count ?? 0),
            'totalRevenue' => (float) ($invoiceStats->total_revenue ?? 0),
            'revenueThisMonth' => (float) ($invoiceStats->month_revenue ?? 0),
            'revenueThisYear' => (float) ($invoiceStats->year_revenue ?? 0),
            'paidInvoices' => (int) ($invoiceStats->paid_count ?? 0),
            'pendingInvoices' => (int) ($invoiceStats->pending_count ?? 0),
            'overdueInvoices' => (int) ($invoiceStats->overdue_count ?? 0),
            'totalQuotes' => (int) ($quoteStats->total_count ?? 0),
            'pendingQuotes' => (int) ($quoteStats->pending_count ?? 0),
            'acceptedQuotes' => (int) ($quoteStats->accepted_count ?? 0),
            'totalCustomers' => $totalCustomers,
            'recentInvoices' => $recentInvoices,
            'monthlyRevenue' => $monthlyRevenue,
        ];
    }
}
