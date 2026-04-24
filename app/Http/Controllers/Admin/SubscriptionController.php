<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = Payment::with(['user', 'planDefinition'])
            ->whereIn('status', ['completed', 'pending']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('plan')) {
            $query->where('plan', $request->plan);
        }

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
        $availablePlans = SubscriptionPlan::orderBy('priority_level')->get();

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

        return view('pages.admin.subscriptions.index', compact('subscriptions', 'stats', 'availablePlans'));
    }

    public function show(Payment $payment)
    {
        $payment->load(['user.listings' => function ($query) {
            $query->latest()->take(5);
        }, 'planDefinition']);

        return view('pages.admin.subscriptions.show', compact('payment'));
    }

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

        if ($payment->user && $payment->user->plan === $payment->plan) {
            $payment->user->update([
                'plan_expires_at' => $payment->expires_at,
            ]);
        }

        Cache::forget('admin_dashboard_stats');

        return back()->with('success', "Abonnement prolongé de {$request->days} jours.");
    }

    public function cancel(Payment $payment)
    {
        $payment->update(['status' => 'refunded']);

        if ($payment->user && $payment->user->plan === $payment->plan) {
            $payment->user->downgradeToFree();
        }

        Cache::forget('admin_dashboard_stats');

        return back()->with('success', 'Abonnement annulé et remboursé.');
    }

    public function create(Request $request)
    {
        $users = User::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'plan']);

        $selectedUser = null;
        if ($request->filled('user_id')) {
            $selectedUser = User::find($request->user_id);
        }

        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('priority_level')
            ->get()
            ->keyBy('slug');

        return view('pages.admin.subscriptions.form', [
            'payment' => new Payment(),
            'users' => $users,
            'selectedUser' => $selectedUser,
            'plans' => $plans,
        ]);
    }

    public function store(Request $request)
    {
        $availablePlanSlugs = SubscriptionPlan::where('is_active', true)
            ->where('slug', '!=', 'gratuit')
            ->pluck('slug')
            ->all();

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan' => 'required|in:' . implode(',', $availablePlanSlugs),
            'duration_days' => 'required|integer|min:1|max:365',
            'amount' => 'nullable|numeric|min:0',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $plan = SubscriptionPlan::where('slug', $validated['plan'])->firstOrFail();
        $durationDays = $validated['duration_days'];
        $amount = $validated['amount'] ?? $plan->price ?? 0;

        $payment = Payment::create([
            'user_id' => $validated['user_id'],
            'plan' => $plan->slug,
            'amount' => $amount,
            'currency' => 'MAD',
            'status' => 'completed',
            'expires_at' => now()->addDays($durationDays),
        ]);

        $user = User::find($validated['user_id']);
        $user->upgradeToPlan($plan->slug, $durationDays);

        Cache::forget('admin_dashboard_stats');

        return redirect()->route('admin.subscriptions.show', $payment)
            ->with('success', 'Abonnement créé avec succès.');
    }

    public function edit(Payment $payment)
    {
        $plans = SubscriptionPlan::orderBy('priority_level')->get()->keyBy('slug');

        return view('pages.admin.subscriptions.form', [
            'payment' => $payment->load('planDefinition'),
            'users' => User::orderBy('name')->get(['id', 'name', 'email', 'plan']),
            'selectedUser' => $payment->user,
            'plans' => $plans,
        ]);
    }

    public function update(Request $request, Payment $payment)
    {
        $availablePlanSlugs = SubscriptionPlan::pluck('slug')->filter(fn ($slug) => $slug !== 'gratuit')->all();

        $validated = $request->validate([
            'plan' => 'required|in:' . implode(',', $availablePlanSlugs),
            'status' => 'required|in:pending,completed,failed,refunded',
            'expires_at' => 'nullable|date',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $payment->update($validated);

        if ($payment->user && $validated['status'] === 'completed') {
            $payment->user->update([
                'plan' => $validated['plan'],
                'plan_expires_at' => $validated['expires_at'] ?? $payment->expires_at,
            ]);
        }

        Cache::forget('admin_dashboard_stats');

        return redirect()->route('admin.subscriptions.show', $payment)
            ->with('success', 'Abonnement mis à jour.');
    }

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
