<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sponsorship;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SponsorshipController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display a listing of sponsorships.
     */
    public function index(Request $request)
    {
        $query = Sponsorship::with(['listing.images', 'user']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Search
        if ($request->filled('q')) {
            $q = $request->q;
            $query->whereHas('listing', function ($listingQuery) use ($q) {
                $listingQuery->where('title', 'like', "%{$q}%");
            })->orWhereHas('user', function ($userQuery) use ($q) {
                $userQuery->where('name', 'like', "%{$q}%");
            });
        }

        $sponsorships = $query->latest()->paginate(20)->withQueryString();

        // Stats
        $stats = [
            'total' => Sponsorship::count(),
            'active' => Sponsorship::where('status', 'active')->count(),
            'pending' => Sponsorship::where('status', 'pending')->count(),
            'expired' => Sponsorship::where('status', 'expired')->count(),
            'revenue' => Sponsorship::where('status', 'active')->sum('amount'),
        ];

        return view('pages.admin.sponsorships.index', compact('sponsorships', 'stats'));
    }

    /**
     * Display a specific sponsorship.
     */
    public function show(Sponsorship $sponsorship)
    {
        $sponsorship->load(['listing.images', 'listing.user', 'user', 'payment']);
        
        return view('pages.admin.sponsorships.show', compact('sponsorship'));
    }

    /**
     * Create a new sponsorship for a listing.
     */
    public function create(Request $request)
    {
        $listing = null;
        if ($request->filled('listing_id')) {
            $listing = Listing::with('user')->find($request->listing_id);
        }

        return view('pages.admin.sponsorships.form', [
            'sponsorship' => new Sponsorship(),
            'listing' => $listing,
            'types' => Sponsorship::TYPES,
            'statuses' => Sponsorship::STATUSES,
        ]);
    }

    /**
     * Store a new sponsorship.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'listing_id' => 'required|exists:listings,id',
            'user_id' => 'required|exists:users,id',
            'type' => 'required|in:basic,premium,premium_plus',
            'status' => 'required|in:pending,active,paused',
            'duration_days' => 'required|integer|min:1|max:90',
            'amount' => 'required|numeric|min:0',
            'starts_at' => 'nullable|date',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        // Set expires_at based on duration
        if ($validated['status'] === 'active') {
            $validated['starts_at'] = $validated['starts_at'] ?? now();
            $validated['expires_at'] = \Carbon\Carbon::parse($validated['starts_at'])->addDays($validated['duration_days']);
        }

        $sponsorship = Sponsorship::create($validated);

        Cache::forget('admin_dashboard_stats');

        return redirect()->route('admin.sponsorships.show', $sponsorship)
            ->with('success', 'Sponsorship créé avec succès.');
    }

    /**
     * Edit a sponsorship.
     */
    public function edit(Sponsorship $sponsorship)
    {
        $sponsorship->load(['listing.user', 'listing.images']);

        return view('pages.admin.sponsorships.form', [
            'sponsorship' => $sponsorship,
            'listing' => $sponsorship->listing,
            'types' => Sponsorship::TYPES,
            'statuses' => Sponsorship::STATUSES,
        ]);
    }

    /**
     * Update a sponsorship.
     */
    public function update(Request $request, Sponsorship $sponsorship)
    {
        $validated = $request->validate([
            'type' => 'required|in:basic,premium,premium_plus',
            'status' => 'required|in:pending,active,paused,expired,cancelled',
            'duration_days' => 'required|integer|min:1|max:90',
            'amount' => 'required|numeric|min:0',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $sponsorship->update($validated);

        Cache::forget('admin_dashboard_stats');

        return redirect()->route('admin.sponsorships.show', $sponsorship)
            ->with('success', 'Sponsorship mis à jour avec succès.');
    }

    /**
     * Activate a pending sponsorship.
     */
    public function activate(Sponsorship $sponsorship)
    {
        $sponsorship->activate();

        Cache::forget('admin_dashboard_stats');

        return back()->with('success', 'Sponsorship activé avec succès.');
    }

    /**
     * Pause an active sponsorship.
     */
    public function pause(Sponsorship $sponsorship)
    {
        $sponsorship->pause();

        return back()->with('success', 'Sponsorship mis en pause.');
    }

    /**
     * Resume a paused sponsorship.
     */
    public function resume(Sponsorship $sponsorship)
    {
        $sponsorship->resume();

        return back()->with('success', 'Sponsorship réactivé.');
    }

    /**
     * Cancel a sponsorship.
     */
    public function cancel(Sponsorship $sponsorship)
    {
        $sponsorship->cancel();

        Cache::forget('admin_dashboard_stats');

        return back()->with('success', 'Sponsorship annulé.');
    }

    /**
     * Delete a sponsorship.
     */
    public function destroy(Sponsorship $sponsorship)
    {
        $sponsorship->delete();

        Cache::forget('admin_dashboard_stats');

        return redirect()->route('admin.sponsorships.index')
            ->with('success', 'Sponsorship supprimé.');
    }
}
