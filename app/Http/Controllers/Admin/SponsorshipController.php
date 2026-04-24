<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sponsorship;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SponsorshipController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = Sponsorship::with(['listing.images', 'user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($searchQuery) use ($q) {
                $searchQuery->whereHas('listing', function ($listingQuery) use ($q) {
                    $listingQuery->where('title', 'like', "%{$q}%");
                })->orWhereHas('user', function ($userQuery) use ($q) {
                    $userQuery->where('name', 'like', "%{$q}%");
                });
            });
        }

        $sponsorships = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total' => Sponsorship::count(),
            'active' => Sponsorship::where('status', 'active')->count(),
            'pending' => Sponsorship::where('status', 'pending')->count(),
            'expired' => Sponsorship::where('status', 'expired')->count(),
            'revenue' => Sponsorship::where('status', 'active')->sum('amount'),
        ];

        return view('pages.admin.sponsorships.index', compact('sponsorships', 'stats'));
    }

    public function show(Sponsorship $sponsorship)
    {
        $sponsorship->load(['listing.images', 'listing.user', 'user', 'payment']);

        return view('pages.admin.sponsorships.show', compact('sponsorship'));
    }

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

        if ($validated['status'] === 'active') {
            $validated['starts_at'] = $validated['starts_at'] ?? now();
            $validated['expires_at'] = \Carbon\Carbon::parse($validated['starts_at'])->addDays($validated['duration_days']);
        }

        $sponsorship = Sponsorship::create($validated);
        $sponsorship->load('listing');
        $sponsorship->syncListingState();

        Cache::forget('admin_dashboard_stats');

        return redirect()->route('admin.sponsorships.show', $sponsorship)
            ->with('success', 'Sponsorship créé avec succès.');
    }

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
        $sponsorship->refresh()->load('listing');
        $sponsorship->syncListingState();

        Cache::forget('admin_dashboard_stats');

        return redirect()->route('admin.sponsorships.show', $sponsorship)
            ->with('success', 'Sponsorship mis à jour avec succès.');
    }

    public function activate(Sponsorship $sponsorship)
    {
        $sponsorship->load('listing');
        $sponsorship->activate();

        Cache::forget('admin_dashboard_stats');
        Cache::forget('featured_listings');

        return back()->with('success', 'Sponsorship activé avec succès.');
    }

    public function pause(Sponsorship $sponsorship)
    {
        $sponsorship->load('listing');
        $sponsorship->pause();

        Cache::forget('admin_dashboard_stats');

        return back()->with('success', 'Sponsorship mis en pause.');
    }

    public function resume(Sponsorship $sponsorship)
    {
        $sponsorship->load('listing');
        $sponsorship->resume();

        Cache::forget('admin_dashboard_stats');

        return back()->with('success', 'Sponsorship réactivé.');
    }

    public function cancel(Sponsorship $sponsorship)
    {
        $sponsorship->load('listing');
        $sponsorship->cancel('Sponsorisation annulée depuis le tableau d\'administration.');

        Cache::forget('admin_dashboard_stats');
        Cache::forget('featured_listings');

        return back()->with('success', 'Sponsorship annulé.');
    }

    public function approveListing(Sponsorship $sponsorship)
    {
        $sponsorship->load('listing');

        if (!$sponsorship->listing) {
            return back()->with('error', 'Annonce introuvable.');
        }

        $sponsorship->listing->update([
            'status' => 'active',
            'rejection_reason' => null,
        ]);

        $sponsorship->syncFromListingStatus('active');

        Cache::forget('admin_dashboard_stats');
        Cache::forget('featured_listings');

        return back()->with('success', 'L\'annonce sponsorisée a été acceptée depuis la page Sponsorisations.');
    }

    public function rejectListing(Request $request, Sponsorship $sponsorship)
    {
        $sponsorship->load('listing');

        if (!$sponsorship->listing) {
            return back()->with('error', 'Annonce introuvable.');
        }

        $reason = trim((string) $request->input('rejection_reason', 'Annonce refusée depuis la gestion des sponsorisations.'));
        if (mb_strlen($reason) < 10) {
            $reason = 'Annonce refusée depuis la gestion des sponsorisations.';
        }

        $sponsorship->listing->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        $sponsorship->cancel($reason);

        Cache::forget('admin_dashboard_stats');
        Cache::forget('featured_listings');

        return back()->with('success', 'L\'annonce sponsorisée a été refusée depuis la page Sponsorisations.');
    }

    public function destroy(Sponsorship $sponsorship)
    {
        $sponsorship->load('listing');
        $listing = $sponsorship->listing;

        $sponsorship->delete();

        if ($listing) {
            $latestSponsorship = $listing->sponsorships()->latest()->first();

            if ($latestSponsorship) {
                $latestSponsorship->syncFromListingStatus($listing->status);
            } else {
                $listing->deactivateSponsorship();
            }
        }

        Cache::forget('admin_dashboard_stats');
        Cache::forget('featured_listings');

        return redirect()->route('admin.sponsorships.index')
            ->with('success', 'Sponsorship supprimé.');
    }
}
