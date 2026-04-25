<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\User;
use App\Models\Report;
use App\Models\Message;
use App\Models\Payment;
use App\Jobs\NotifyListingApproved;
use App\Services\AiService;
use App\Services\MediaStorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function __construct(
        protected AiService $ai,
        protected MediaStorageService $media,
    ) {
        $this->middleware(['auth', 'role:admin']);
    }

    // ─── Dashboard ────────────────────────────────────────────────────────────

    public function dashboard()
    {
        $stats = Cache::remember('admin_dashboard_stats', 300, fn() => [
            'users_total'        => User::count(),
            'users_agents'       => User::byRole('agent')->count(),
            'users_today'        => User::whereDate('created_at', today())->count(),
            'users_this_week'    => User::where('created_at', '>=', now()->startOfWeek())->count(),
            'listings_total'     => Listing::count(),
            'listings_active'    => Listing::active()->count(),
            'listings_pending'   => Listing::where('status', 'pending')->count(),
            'listings_today'     => Listing::whereDate('created_at', today())->count(),
            'reports_pending'    => Report::where('status', 'pending')->count(),
            'messages_total'     => Message::count(),
            'payments_total'     => Payment::count(),
            'payments_completed' => Payment::where('status', 'completed')->count(),
            'payments_pending'   => Payment::where('status', 'pending')->count(),
            'payments_failed'    => Payment::where('status', 'failed')->count(),
            'payments_refunded'  => Payment::where('status', 'refunded')->count(),
            'revenue_month'      => Payment::where('status', 'completed')
                                           ->whereMonth('created_at', now()->month)
                                           ->sum('amount'),
            'revenue_total'      => Payment::where('status', 'completed')->sum('amount'),
        ]);

        // Graphique inscriptions 30 derniers jours
        $userGrowth = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Graphique annonces par type
        $listingsByType = Listing::selectRaw('transaction_type, COUNT(*) as count')
            ->where('status', 'active')
            ->groupBy('transaction_type')
            ->get();

        // Graphique revenus 6 derniers mois
        $revenueByMonth = collect();
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenueByMonth->push((object) [
                'month' => $month->format('Y-m'),
                'total' => Payment::where('status', 'completed')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('amount'),
            ]);
        }

        // Annonces en attente
        $pendingListings = Listing::with(['user', 'images'])
            ->pending()
            ->orderByDesc('is_sponsored')
            ->latest()
            ->take(8)
            ->get();

        // Signalements récents
        $recentReports = Report::with(['listing', 'user'])
            ->where('status', 'pending')
            ->latest()
            ->take(6)
            ->get();

        $sponsoredListings = Listing::with(['user', 'images', 'sponsorship'])
            ->where('is_sponsored', true)
            ->orderByDesc('sponsored_until')
            ->latest()
            ->take(6)
            ->get();

        // Top villes
        $listingsByCity = Listing::active()
            ->selectRaw('city, count(*) as total')
            ->groupBy('city')
            ->orderByDesc('total')
            ->take(8)
            ->get();

        // Derniers utilisateurs inscrits
        $latestUsers = User::latest()->take(5)->get();

        // Derniers paiements
        $latestPayments = Payment::with('user')
            ->where('status', 'completed')
            ->latest()
            ->take(5)
            ->get();

        return view('pages.dashboards.admin.index', compact(
            'stats', 'pendingListings', 'recentReports', 'sponsoredListings', 'listingsByCity',
            'userGrowth', 'listingsByType', 'revenueByMonth', 'latestUsers', 'latestPayments'
        ));
    }

    // ─── Profil ───────────────────────────────────────────────────────────────

    public function profile()
    {
        return view('pages.admin.profile', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'   => 'required|string|max:100',
            'email'  => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone'  => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                $this->media->delete($user->avatar);
            }
            $validated['avatar'] = $this->media->uploadUploadedFile($request->file('avatar'), 'avatars');
        }

        $user->update($validated);

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    // ─── Annonces ─────────────────────────────────────────────────────────────

    public function listingsIndex(Request $request)
    {
        $query = Listing::with(['user', 'images']);

        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('city'))     $query->where('city', $request->city);
        if ($request->filled('type'))     $query->where('transaction_type', $request->type);
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($sq) => $sq->where('title', 'like', "%{$q}%")->orWhere('city', 'like', "%{$q}%"));
        }

        $listings = $query->latest()->paginate(20)->withQueryString();

        return view('pages.admin.listings.index', [
            'listings'         => $listings,
            'statuses'         => Listing::STATUSES,
            'cities'           => Listing::CITIES,
            'transactionTypes' => Listing::TRANSACTION_TYPES,
        ]);
    }

    public function listingShow(Listing $listing)
    {
        $listing->load(['user', 'images', 'reports.user', 'messages.sender', 'sponsorship']);
        $aiModeration = $listing->aiModeration;

        // Analyse IA si pas encore faite
        if (!$aiModeration && $listing->status === 'pending') {
            $result = $this->ai->moderateListing($listing);
            $listing->aiModeration()->updateOrCreate([], $result);
            $aiModeration = $listing->aiModeration;
        }

        return view('pages.admin.listings.show', compact('listing', 'aiModeration'));
    }

    public function listingApprove(Listing $listing)
    {
        $listing->update(['status' => 'active', 'rejection_reason' => null]);
        $this->syncLatestSponsorshipForListing($listing);

        Cache::forget('admin_dashboard_stats');
        Cache::forget('featured_listings');
        Cache::forget('payment_stats');

        // Dispatch job asynchrone via Horizon
        NotifyListingApproved::dispatch($listing)->onQueue('notifications');

        return back()->with('success', "L'annonce « {$listing->title} » a été validée. Notifications envoyées.");
    }

    public function listingReject(Request $request, Listing $listing)
    {
        $request->validate(['rejection_reason' => 'required|string|min:10|max:500']);

        $listing->update(['status' => 'rejected', 'rejection_reason' => $request->rejection_reason]);
        $this->syncLatestSponsorshipForListing($listing);

        Cache::forget('admin_dashboard_stats');
        Cache::forget('featured_listings');

        // Notification asynchrone
        \App\Jobs\NotifyListingRejected::dispatch($listing)->onQueue('notifications');

        return back()->with('success', "L'annonce a été refusée. L'auteur a été notifié.");
    }

    public function listingFeature(Listing $listing)
    {
        $listing->update(['featured' => !$listing->featured]);
        Cache::forget('featured_listings');
        Cache::forget('admin_dashboard_stats');

        $action = $listing->featured ? 'mise en avant' : 'retirée des coups de cœur';
        return back()->with('success', "L'annonce a été {$action}.");
    }

    public function listingSetPriority(Request $request, Listing $listing)
    {
        $request->validate([
            'priority' => 'required|integer|min:0|max:10',
        ]);

        $listing->update(['priority' => $request->priority]);
        Cache::forget('featured_listings');
        Cache::forget('admin_dashboard_stats');

        return back()->with('success', "La priorité a été définie sur {$listing->priority} ({$listing->priority_label}).");
    }

    public function listingDestroy(Listing $listing)
    {
        foreach ($listing->images as $img) {
            $this->media->delete($img->path);
        }
        $listing->forceDelete();

        return redirect()->route('admin.listings.index')
            ->with('success', 'Annonce supprimée définitivement.');
    }

    public function listingCreate()
    {
        $users = User::where('is_active', true)->orderBy('name')->get(['id', 'name', 'email']);
        
        return view('pages.admin.listings.form', [
            'listing' => new Listing(),
            'users' => $users,
            'statuses' => Listing::STATUSES,
            'transactionTypes' => Listing::TRANSACTION_TYPES,
            'propertyTypes' => Listing::PROPERTY_TYPES,
            'cities' => Listing::CITIES,
        ]);
    }

    public function listingStore(Request $request)
    {
        $validated = $request->validate([
            'user_id'          => 'required|exists:users,id',
            'title'            => 'required|string|max:255',
            'description'      => 'required|string|min:20',
            'price'            => 'required|numeric|min:0',
            'surface'          => 'nullable|numeric|min:0',
            'rooms'            => 'nullable|integer|min:0',
            'bathrooms'        => 'nullable|integer|min:0',
            'floor'            => 'nullable|integer',
            'transaction_type' => 'required|in:' . implode(',', array_keys(Listing::TRANSACTION_TYPES)),
            'property_type'    => 'required|in:' . implode(',', array_keys(Listing::PROPERTY_TYPES)),
            'city'             => 'required|string|max:100',
            'zone'             => 'nullable|string|max:100',
            'address'          => 'nullable|string|max:255',
            'latitude'         => 'nullable|numeric',
            'longitude'        => 'nullable|numeric',
            'furnished'        => 'boolean',
            'parking'          => 'boolean',
            'elevator'         => 'boolean',
            'pool'             => 'boolean',
            'garden'           => 'boolean',
            'terrace'          => 'boolean',
            'security'         => 'boolean',
            'status'           => 'required|in:' . implode(',', array_keys(Listing::STATUSES)),
            'featured'         => 'boolean',
            'priority'         => 'nullable|integer|min:0|max:10',
            'images'           => 'nullable|array',
            'images.*'         => 'image|mimes:jpg,jpeg,png,webp|max:10240',
            'image_urls'       => 'nullable|array',
            'image_urls.*'     => 'url',
        ]);

        $imageUrls = $validated['image_urls'] ?? [];
        unset($validated['image_urls']);

        $listing = Listing::create($validated);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $i => $image) {
                $path = $this->media->uploadUploadedFile($image, "listings/{$listing->id}");
                $listing->images()->create(['path' => $path, 'order' => $i]);
                if ($i === 0) {
                    $listing->update(['thumbnail' => $path]);
                }
            }
        }

        if (!empty($imageUrls)) {
            $order = $listing->images()->max('order') ?? -1;

            foreach ($imageUrls as $url) {
                try {
                    $path = $this->media->uploadFromUrl($url, "listings/{$listing->id}");

                    if ($path) {
                        $order++;
                        $listing->images()->create(['path' => $path, 'order' => $order]);

                        if (empty($listing->thumbnail)) {
                            $listing->update(['thumbnail' => $path]);
                        }
                    }
                } catch (\Exception $e) {
                    // Ignore URL download failures and continue with the others.
                }
            }
        }

        Cache::forget('admin_dashboard_stats');
        Cache::forget('featured_listings');

        return redirect()->route('admin.listings.show', $listing)
            ->with('success', "L'annonce a été créée avec succès.");
    }

    public function listingEdit(Listing $listing)
    {
        $listing->load('images');
        $users = User::where('is_active', true)->orderBy('name')->get(['id', 'name', 'email']);
        
        return view('pages.admin.listings.form', [
            'listing' => $listing,
            'users' => $users,
            'statuses' => Listing::STATUSES,
            'transactionTypes' => Listing::TRANSACTION_TYPES,
            'propertyTypes' => Listing::PROPERTY_TYPES,
            'cities' => Listing::CITIES,
        ]);
    }

    public function listingUpdate(Request $request, Listing $listing)
    {
        $validated = $request->validate([
            'user_id'          => 'required|exists:users,id',
            'title'            => 'required|string|max:255',
            'description'      => 'required|string|min:20',
            'price'            => 'required|numeric|min:0',
            'surface'          => 'nullable|numeric|min:0',
            'rooms'            => 'nullable|integer|min:0',
            'bathrooms'        => 'nullable|integer|min:0',
            'floor'            => 'nullable|integer',
            'transaction_type' => 'required|in:' . implode(',', array_keys(Listing::TRANSACTION_TYPES)),
            'property_type'    => 'required|in:' . implode(',', array_keys(Listing::PROPERTY_TYPES)),
            'city'             => 'required|string|max:100',
            'zone'             => 'nullable|string|max:100',
            'address'          => 'nullable|string|max:255',
            'latitude'         => 'nullable|numeric',
            'longitude'        => 'nullable|numeric',
            'furnished'        => 'boolean',
            'parking'          => 'boolean',
            'elevator'         => 'boolean',
            'pool'             => 'boolean',
            'garden'           => 'boolean',
            'terrace'          => 'boolean',
            'security'         => 'boolean',
            'status'           => 'required|in:' . implode(',', array_keys(Listing::STATUSES)),
            'featured'         => 'boolean',
            'priority'         => 'nullable|integer|min:0|max:10',
            'images'           => 'nullable|array',
            'images.*'         => 'image|mimes:jpg,jpeg,png,webp|max:10240',
            'image_urls'       => 'nullable|array',
            'image_urls.*'     => 'url',
            'delete_images'    => 'nullable|array',
            'delete_images.*'  => 'integer|exists:listing_images,id',
        ]);

        $imageUrls = $validated['image_urls'] ?? [];
        unset($validated['image_urls']);

        $listing->update($validated);

        if ($request->has('delete_images')) {
            $imagesToDelete = $listing->images()->whereIn('id', $request->delete_images)->get();
            foreach ($imagesToDelete as $img) {
                $this->media->delete($img->path);
                $img->delete();
            }
        }

        if ($request->hasFile('images')) {
            $startIndex = $listing->images()->max('order') ?? -1;
            foreach ($request->file('images') as $i => $image) {
                $order = $startIndex + $i + 1;
                $path = $this->media->uploadUploadedFile($image, "listings/{$listing->id}");
                $listing->images()->create(['path' => $path, 'order' => $order]);
            }
        }

        if (!empty($imageUrls)) {
            $startIndex = $listing->images()->max('order') ?? -1;

            foreach ($imageUrls as $url) {
                try {
                    $path = $this->media->uploadFromUrl($url, "listings/{$listing->id}");

                    if ($path) {
                        $startIndex++;
                        $listing->images()->create(['path' => $path, 'order' => $startIndex]);
                    }
                } catch (\Exception $e) {
                    // Ignore URL download failures and continue with the others.
                }
            }
        }

        // Update Thumbnail if it was deleted or doesn't exist
        $firstImage = $listing->images()->orderBy('order')->first();
        if ($firstImage) {
            $listing->update(['thumbnail' => $firstImage->path]);
        } else {
            $listing->update(['thumbnail' => null]);
        }

        Cache::forget('admin_dashboard_stats');
        Cache::forget('featured_listings');

        return redirect()->route('admin.listings.show', $listing)
            ->with('success', "L'annonce a été mise à jour avec succès.");
    }

    public function aiGenerateDescription(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'            => 'required|string|min:5|max:255',
            'property_type'    => 'required|string',
            'transaction_type' => 'required|string',
            'city'             => 'nullable|string',
            'zone'             => 'nullable|string',
            'surface'          => 'nullable|numeric',
            'rooms'            => 'nullable|integer',
            'bathrooms'        => 'nullable|integer',
            'price'            => 'nullable|numeric',
            'furnished'        => 'boolean',
            'parking'          => 'boolean',
            'elevator'         => 'boolean',
            'pool'             => 'boolean',
            'garden'           => 'boolean',
            'terrace'          => 'boolean',
            'security'         => 'boolean',
        ]);

        $description = $this->ai->generateDescription($validated);

        if (empty($description)) {
            return response()->json(['error' => 'IA indisponible, réessayez.'], 503);
        }

        return response()->json(['description' => $description]);
    }

    public function listingUpdateStatus(Request $request, Listing $listing)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(Listing::STATUSES)),
        ]);

        $oldStatus = $listing->status;
        $newStatus = $request->status;

        $listing->update([
            'status' => $newStatus,
            'rejection_reason' => $newStatus === 'rejected'
                ? ($listing->rejection_reason ?: 'Annonce refusée depuis la gestion des annonces.')
                : null,
        ]);

        $this->syncLatestSponsorshipForListing($listing);

        Cache::forget('admin_dashboard_stats');
        Cache::forget('featured_listings');

        return back()->with('success', "Le statut a été modifié de « " . Listing::STATUSES[$oldStatus] . " » à « " . Listing::STATUSES[$newStatus] . " ».");
    }

    protected function syncLatestSponsorshipForListing(Listing $listing): void
    {
        $sponsorship = $listing->sponsorships()->latest()->first();

        if (!$sponsorship) {
            if ($listing->status !== 'active') {
                $listing->deactivateSponsorship();
            }
            return;
        }

        $sponsorship->syncFromListingStatus($listing->status);
    }

    // ─── Utilisateurs ─────────────────────────────────────────────────────────

    public function usersIndex(Request $request)
    {
        $query = User::withCount('listings');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true)->whereNull('banned_at');
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false)->whereNull('banned_at');
            } elseif ($request->status === 'banned') {
                $query->whereNotNull('banned_at');
            }
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn($sq) => $sq->where('name', 'like', "%{$q}%")->orWhere('email', 'like', "%{$q}%"));
        }

        $users = $query->latest()->paginate(20)->withQueryString();
        
        return view('pages.admin.users.index', compact('users'));
    }

    public function userShow(User $user)
    {
        $user->load(['listings.images', 'listings' => fn($q) => $q->latest()]);
        
        return view('pages.admin.users.show', compact('user'));
    }

    public function userCreate()
    {
        return view('pages.admin.users.form', [
            'user' => new User(),
        ]);
    }

    public function userStore(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email',
            'phone'     => 'nullable|string|max:20',
            'role'      => 'required|in:admin,agent,particulier',
            'password'  => 'required|string|min:8|confirmed',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        $validated['is_active'] = true;

        $user = User::create($validated);
        Cache::forget('admin_dashboard_stats');

        return redirect()->route('admin.users.show', $user)
            ->with('success', "L'utilisateur {$user->name} a été créé avec succès.");
    }

    public function userEdit(User $user)
    {
        return view('pages.admin.users.form', compact('user'));
    }

    public function userUpdate(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone'     => 'nullable|string|max:20',
            'role'      => 'required|in:admin,agent,particulier',
            'password'  => 'nullable|string|min:8|confirmed',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        Cache::forget('admin_dashboard_stats');

        return redirect()->route('admin.users.show', $user)
            ->with('success', "L'utilisateur {$user->name} a été mis à jour.");
    }

    public function userToggleActive(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Vous ne pouvez pas désactiver votre propre compte.']);
        }

        $user->update(['is_active' => !$user->is_active]);

        if (! $user->is_active) {
            $user->revokeActiveSessions();
        }

        $action = $user->is_active ? 'activé' : 'désactivé';
        Cache::forget('admin_dashboard_stats');

        return back()->with('success', "Compte {$action} avec succès.");
    }

    public function userBan(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Vous ne pouvez pas bannir votre propre compte.']);
        }

        $request->validate([
            'ban_reason' => 'nullable|string|max:500',
        ]);

        $user->ban($request->ban_reason);
        $user->revokeActiveSessions();
        Cache::forget('admin_dashboard_stats');

        return back()->with('success', "L'utilisateur {$user->name} a été banni.");
    }

    public function userUnban(User $user)
    {
        $user->unban();
        Cache::forget('admin_dashboard_stats');

        return back()->with('success', "L'utilisateur {$user->name} a été réactivé.");
    }

    public function userDestroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Vous ne pouvez pas supprimer votre propre compte.']);
        }

        foreach ($user->listings as $listing) {
            foreach ($listing->images as $img) {
                $this->media->delete($img->path);
            }
        }

        $user->revokeActiveSessions();
        $user->delete();
        Cache::forget('admin_dashboard_stats');

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur supprimé.');
    }

    // ─── Signalements ─────────────────────────────────────────────────────────

    public function reportsIndex(Request $request)
    {
        $query = Report::with(['listing', 'user']);

        if ($request->filled('status')) $query->where('status', $request->status);

        $reports = $query->latest()->paginate(20)->withQueryString();
        $reasons = Report::REASONS;

        return view('pages.admin.reports.index', compact('reports', 'reasons'));
    }

    public function reportShow(Report $report)
    {
        $report->load(['listing.user', 'listing.images', 'user']);
        
        return view('pages.admin.reports.show', compact('report'));
    }

    public function reportResolve(Report $report)
    {
        $report->update(['status' => 'resolved']);
        Cache::forget('admin_dashboard_stats');
        return back()->with('success', 'Signalement marqué comme résolu.');
    }

    public function reportDismiss(Report $report)
    {
        $report->update(['status' => 'reviewed']);
        return back()->with('success', 'Signalement classé sans suite.');
    }

    public function reportForward(Report $report)
    {
        $listing = $report->listing;

        if (!$listing || !$listing->user_id) {
            return back()->withErrors(['error' => 'Cette annonce n\'existe plus ou n\'a pas de propriétaire assigné.']);
        }

        $reasonText = Report::REASONS[$report->reason] ?? 'Signalement';
        $message = $report->description ? ' Message: ' . $report->description : '';

        \Illuminate\Support\Facades\DB::table('user_notifications')->insert([
            'user_id' => $listing->user_id,
            'type' => 'listing_reported',
            'title' => 'Annonce signalée par un utilisateur',
            'body' => "Votre annonce « {$listing->title} » a fait l'objet d'un signalement pour : {$reasonText}.{$message} L'administration vous invite à vérifier votre annonce.",
            'url' => route('user.listings.show', $listing->id),
            'read' => false,
            'data' => json_encode(['report_id' => $report->id]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $report->update(['status' => 'reviewed']);

        return back()->with('success', "Le signalement a bien été transféré à l'auteur de l'annonce.");
    }

    public function reportDeleteListing(Report $report)
    {
        $listing = $report->listing;
        
        if (!$listing) {
            return back()->withErrors(['error' => 'Cette annonce n\'existe plus.']);
        }

        // Delete listing images
        foreach ($listing->images as $img) {
            $this->media->delete($img->path);
        }

        $listingTitle = $listing->title;
        $listing->forceDelete();
        
        // Mark report as resolved
        $report->update(['status' => 'resolved']);
        
        Cache::forget('admin_dashboard_stats');
        Cache::forget('featured_listings');

        return back()->with('success', "L'annonce « {$listingTitle} » a été supprimée.");
    }

    public function reportBanUser(Report $report)
    {
        $listing = $report->listing;
        
        if (!$listing || !$listing->user) {
            return back()->withErrors(['error' => 'L\'auteur de cette annonce n\'est plus disponible.']);
        }

        $user = $listing->user;
        $userName = $user->name;
        
        // Ban the user
        $user->ban("Annonce signalée: {$report->reason_label}");
        $user->revokeActiveSessions();
        
        // Mark report as resolved
        $report->update(['status' => 'resolved']);
        
        Cache::forget('admin_dashboard_stats');

        return back()->with('success', "L'utilisateur « {$userName} » a été banni.");
    }

    // ─── API JSON pour graphiques ─────────────────────────────────────────────

    public function statsApi(Request $request)
    {
        $period = $request->input('period', '30');

        $data = [
            'users'    => User::selectRaw('DATE(created_at) as d, COUNT(*) as n')
                ->where('created_at', '>=', now()->subDays($period))
                ->groupBy('d')->orderBy('d')->get(),
            'listings' => Listing::selectRaw('DATE(created_at) as d, COUNT(*) as n')
                ->where('created_at', '>=', now()->subDays($period))
                ->groupBy('d')->orderBy('d')->get(),
            'revenue'  => Payment::selectRaw('DATE(created_at) as d, SUM(amount) as n')
                ->where('created_at', '>=', now()->subDays($period))
                ->where('status', 'completed')
                ->groupBy('d')->orderBy('d')->get(),
        ];

        return response()->json($data);
    }
}