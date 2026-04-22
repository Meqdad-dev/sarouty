<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display a listing of subscriptions.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['user'])
            ->whereIn('status', ['completed', 'pending']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by plan
        if ($request->filled('plan')) {
            $query->where('plan', $request->plan);
        }

        // Filter by active/expired
        if ($request->filled('active')) {
            if ($request->active === 'yes') {
                $query->where('expires_at', '>', now());
            } elseif ($request->active === 'no') {
                $query->where(function ($q) {
                    $q->whereNull('expires_at')
                        ->orWhere('expires_at', '<=', now());
                });
            }
        }

        // Search
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->whereHas('user', function ($userQuery) use ($q) {
                    $userQuery->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                })->orWhere('stripe_session_id', 'like', "%{$q}%")
                    ->orWhere('stripe_payment_intent', 'like', "%{$q}%");
            });
        }

        $subscriptions = $query->latest()->paginate(20)->withQueryString();

        // Stats
        $stats = [
            'total' => Payment::whereIn('status', ['completed', 'pending'])->count(),
            'active' => Payment::where('status', 'completed')
                ->where('expires_at', '>', now())
                ->count(),
            'revenue' => Payment::where('status', 'completed')->sum('amount'),
            'mrr' => Payment::where('status', 'completed')
                ->where('created_at', '>=', now()->subMonth())
                ->sum('amount'),
            'by_plan' => [
                'starter' => User::where('plan', 'starter')->where('plan_expires_at', '>', now())->count(),
                'pro' => User::where('plan', 'pro')->where('plan_expires_at', '>', now())->count(),
                'agence' => User::where('plan', 'agence')->where('plan_expires_at', '>', now())->count(),
            ],
        ];

        return view('pages.admin.subscriptions.index', compact('subscriptions', 'stats'));
    }

    /**
     * Display a specific subscription/payment.
     */
    public function show(Payment $payment)
    {
        $payment->load(['user.listings' => function ($query) {
            $query->latest()->take(5);
        }]);

        return view('pages.admin.subscriptions.show', compact('payment'));
    }

    /**
     * Extend a subscription.
     */
    public function extend(Request $request, Payment $payment)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365',
        ]);

        if ($payment->expires_at) {
            $payment->expires_at = $payment->expires_at->addDays($request->days);
        } else {
            $payment->expires_at = now()->addDays($request->days);
        }

        $payment->save();

        // Also update user's plan_expires_at
        if ($payment->user && $payment->user->plan === $payment->plan) {
            $payment->user->update([
                'plan_expires_at' => $payment->expires_at,
            ]);
        }

        Cache::forget('admin_dashboard_stats');

        return back()->with('success', "Abonnement prolongé de {$request->days} jours.");
    }

    /**
     * Cancel a subscription.
     */
    public function cancel(Payment $payment)
    {
        $payment->update(['status' => 'refunded']);

        // Downgrade user to free if this was their active plan
        if ($payment->user && $payment->user->plan === $payment->plan) {
            $payment->user->downgradeToFree();
        }

        Cache::forget('admin_dashboard_stats');

        return back()->with('success', 'Abonnement annulé et remboursé.');
    }

    /**
     * Create a new subscription for a user (admin override).
     */
    public function create(Request $request)
    {
        $users = User::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'plan']);

        $selectedUser = null;
        if ($request->filled('user_id')) {
            $selectedUser = User::find($request->user_id);
        }

        return view('pages.admin.subscriptions.form', [
            'payment' => new Payment(),
            'users' => $users,
            'selectedUser' => $selectedUser,
            'plans' => Payment::PLANS,
        ]);
    }

    /**
     * Store a new subscription (admin override).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan' => 'required|in:starter,pro,agence',
            'duration_days' => 'required|integer|min:1|max:365',
            'amount' => 'nullable|numeric|min:0',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $plan = $validated['plan'];
        $durationDays = $validated['duration_days'];
        $amount = $validated['amount'] ?? Payment::PLANS[$plan]['price'] ?? 0;

        // Create payment record
        $payment = Payment::create([
            'user_id' => $validated['user_id'],
            'plan' => $plan,
            'amount' => $amount,
            'currency' => 'MAD',
            'status' => 'completed',
            'expires_at' => now()->addDays($durationDays),
        ]);

        // Upgrade user
        $user = User::find($validated['user_id']);
        $user->upgradeToPlan($plan, $durationDays);

        Cache::forget('admin_dashboard_stats');

        return redirect()->route('admin.subscriptions.show', $payment)
            ->with('success', 'Abonnement créé avec succès.');
    }

    /**
     * Edit subscription.
     */
    public function edit(Payment $payment)
    {
        return view('pages.admin.subscriptions.form', [
            'payment' => $payment,
            'users' => User::orderBy('name')->get(['id', 'name', 'email', 'plan']),
            'selectedUser' => $payment->user,
            'plans' => Payment::PLANS,
        ]);
    }

    /**
     * Update subscription.
     */
    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'plan' => 'required|in:starter,pro,agence',
            'status' => 'required|in:pending,completed,failed,refunded',
            'expires_at' => 'nullable|date',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $payment->update($validated);

        Cache::forget('admin_dashboard_stats');

        return redirect()->route('admin.subscriptions.show', $payment)
            ->with('success', 'Abonnement mis à jour.');
    }

    /**
     * Get subscription statistics for dashboard.
     */
    public function stats()
    {
        return response()->json([
            'active_subscriptions' => Payment::where('status', 'completed')
                ->where('expires_at', '>', now())
                ->count(),
            'mrr' => Payment::where('status', 'completed')
                ->where('created_at', '>=', now()->subMonth())
                ->sum('amount'),
            'by_plan' => [
                'starter' => User::where('plan', 'starter')->where('plan_expires_at', '>', now())->count(),
                'pro' => User::where('plan', 'pro')->where('plan_expires_at', '>', now())->count(),
                'agence' => User::where('plan', 'agence')->where('plan_expires_at', '>', now())->count(),
            ],
        ]);
    }
}
