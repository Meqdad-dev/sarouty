<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display a listing of payments with history.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['user']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by plan
        if ($request->filled('plan')) {
            $query->where('plan', $request->plan);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Quick date filter
        if ($request->filled('period')) {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                    break;
                case 'year':
                    $query->whereYear('created_at', now()->year);
                    break;
            }
        }

        // Search
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('stripe_session_id', 'like', "%{$q}%")
                    ->orWhere('stripe_payment_intent', 'like', "%{$q}%")
                    ->orWhereHas('user', fn ($userQuery) => $userQuery
                        ->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%"));
            });
        }

        $payments = $query->latest()->paginate(25)->withQueryString();

        // Comprehensive statistics
        $stats = $this->getStats();

        // Monthly revenue chart data (last 12 months)
        $chartData = $this->getMonthlyRevenueChart();

        return view('pages.admin.payments.index', compact('payments', 'stats', 'chartData'));
    }

    /**
     * Display a specific payment.
     */
    public function show(Payment $payment)
    {
        $payment->load(['user.listings' => function ($query) {
            $query->latest()->take(5);
        }]);

        // Get user's payment history
        $userPayments = Payment::where('user_id', $payment->user_id)
            ->where('id', '!=', $payment->id)
            ->latest()
            ->take(10)
            ->get();

        return view('pages.admin.payments.show', compact('payment', 'userPayments'));
    }

    /**
     * Refund a payment.
     */
    public function refund(Payment $payment)
    {
        if ($payment->status !== 'completed') {
            return back()->with('error', 'Seuls les paiements complétés peuvent être remboursés.');
        }

        // Update payment status
        $payment->update(['status' => 'refunded']);

        // If user has this plan active, downgrade to free
        if ($payment->user && $payment->user->plan === $payment->plan) {
            $payment->user->downgradeToFree();
        }

        Cache::forget('admin_dashboard_stats');

        return back()->with('success', 'Paiement remboursé avec succès.');
    }

    /**
     * Export payments to CSV.
     */
    public function export(Request $request)
    {
        $query = Payment::with(['user']);

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('plan')) {
            $query->where('plan', $request->plan);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->latest()->get();

        $filename = 'paiements_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($payments) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
            
            fputcsv($file, ['ID', 'Utilisateur', 'Email', 'Plan', 'Montant', 'Devise', 'Statut', 'Transaction', 'Date']);

            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->id,
                    $payment->user->name ?? 'N/A',
                    $payment->user->email ?? 'N/A',
                    $payment->plan_label,
                    $payment->amount,
                    $payment->currency ?? 'MAD',
                    $payment->status_label,
                    $payment->stripe_payment_intent ?? $payment->stripe_session_id ?? 'Admin',
                    $payment->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get payment statistics.
     */
    private function getStats(): array
    {
        return Cache::remember('payment_stats', 300, function () {
            $today = today();
            $thisMonth = now()->startOfMonth();
            $lastMonth = now()->subMonth()->startOfMonth();
            $thisYear = now()->startOfYear();

            return [
                'total' => Payment::count(),
                'completed' => Payment::where('status', 'completed')->count(),
                'pending' => Payment::where('status', 'pending')->count(),
                'failed' => Payment::where('status', 'failed')->count(),
                'refunded' => Payment::where('status', 'refunded')->count(),
                
                // Revenue
                'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
                'monthly_revenue' => Payment::where('status', 'completed')
                    ->where('created_at', '>=', $thisMonth)
                    ->sum('amount'),
                'last_month_revenue' => Payment::where('status', 'completed')
                    ->whereBetween('created_at', [$lastMonth, $thisMonth])
                    ->sum('amount'),
                'yearly_revenue' => Payment::where('status', 'completed')
                    ->where('created_at', '>=', $thisYear)
                    ->sum('amount'),
                'today_revenue' => Payment::where('status', 'completed')
                    ->whereDate('created_at', $today)
                    ->sum('amount'),
                
                // Average
                'average_payment' => Payment::where('status', 'completed')->avg('amount') ?? 0,
                
                // By plan
                'by_plan' => [
                    'starter' => Payment::where('plan', 'starter')->where('status', 'completed')->count(),
                    'pro' => Payment::where('plan', 'pro')->where('status', 'completed')->count(),
                    'agence' => Payment::where('plan', 'agence')->where('status', 'completed')->count(),
                ],
                
                // Revenue by plan
                'revenue_by_plan' => [
                    'starter' => Payment::where('plan', 'starter')->where('status', 'completed')->sum('amount'),
                    'pro' => Payment::where('plan', 'pro')->where('status', 'completed')->sum('amount'),
                    'agence' => Payment::where('plan', 'agence')->where('status', 'completed')->sum('amount'),
                ],
            ];
        });
    }

    /**
     * Get monthly revenue chart data.
     */
    private function getMonthlyRevenueChart(): array
    {
        $data = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenue = Payment::where('status', 'completed')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('amount');
            
            $data[] = [
                'month' => $month->translatedFormat('M Y'),
                'revenue' => (float) $revenue,
            ];
        }

        return $data;
    }
}

