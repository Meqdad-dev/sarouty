<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Favorite;
use App\Models\Sponsorship;
use App\Services\AiService;
use App\Services\EmailService;
use App\Services\GeoService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class UserListingController extends Controller
{
    public function __construct(
        protected AiService $ai,
        protected EmailService $email,
        protected GeoService $geo,
        protected PaymentService $payments,
    ) {
        $this->middleware('auth');
    }

    // ─── Dashboard ────────────────────────────────────────────────────────────

    public function dashboard()
    {
        $user = auth()->user();

        $stats = [
            'listings_total'   => $user->listings()->count(),
            'listings_active'  => $user->listings()->where('status', 'active')->count(),
            'listings_pending' => $user->listings()->where('status', 'pending')->count(),
            'total_views'      => $user->listings()->sum('views'),
            'favorites_count'  => $user->favorites()->count(),
            'messages_unread'  => \App\Models\Message::where('receiver_id', $user->id)->where('is_read', false)->count(),
        ];

        // For clients, we show their favorite listings on the dashboard instead of their own listings
        if ($user->isClient()) {
            return view('pages.dashboards.client.index', [
                'favorites' => $user->favoriteListings()->with('images')->latest()->paginate(8)
            ]);
        }

        $listings = $user->listings()
            ->with('images')
            ->latest()
            ->paginate(8);

        $plan = $this->payments->getPlan($user->plan ?? 'gratuit');
        $listingsUsed = $user->listings()->whereMonth('created_at', now()->month)->count();
        $canCreateSponsoredListing = $user->canCreateSponsoredListing();
        $sponsoredActiveCount = $user->listings()
            ->where('is_sponsored', true)
            ->where('sponsored_until', '>', now())
            ->count();

        $viewName = $user->isAgent() ? 'pages.dashboards.agent.index' : 'pages.dashboards.particulier.index';

        return view($viewName, compact(
            'stats',
            'listings',
            'plan',
            'listingsUsed',
            'canCreateSponsoredListing',
            'sponsoredActiveCount'
        ));
    }

    // ─── Profil ───────────────────────────────────────────────────────────────

    public function profile()
    {
        return view('pages.user.profile', ['user' => auth()->user()]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'   => 'required|string|max:100',
            'phone'  => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    // ─── CRUD Annonces ────────────────────────────────────────────────────────

    public function create()
    {
        $user = auth()->user();

        if (!$user->canCreateListing()) {
            $quota = $user->plan_quota;
            return redirect()->route('user.dashboard')
                ->withErrors(['quota' => "Vous avez atteint votre quota de {$quota} annonces ce mois. Passez a un plan superieur."]);
        }

        return view('pages.user.listings.create', [
            'cities' => Listing::CITIES,
            'propertyTypes' => Listing::PROPERTY_TYPES,
            'transactionTypes' => Listing::TRANSACTION_TYPES,
            'pricePeriods' => Listing::PRICE_PERIODS,
            'canCreateSponsoredListing' => $user->canCreateSponsoredListing(),
            'sponsoredDurationDays' => $user->sponsored_listing_duration_days,
            'listingDurationDays' => $user->listing_duration_days,
            'maxImages' => $user->max_images_per_ad,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->canCreateListing()) {
            $quota = $user->plan_quota;
            return redirect()->route('user.dashboard')
                ->withErrors(['quota' => "Vous avez atteint votre quota de {$quota} annonces ce mois. Passez a un plan superieur."]);
        }

        $maxImages = $user->max_images_per_ad;

        $validated = $request->validate([
            'title'            => 'required|string|min:10|max:150',
            'description'      => 'required|string|min:50|max:5000',
            'price'            => 'required|numeric|min:1|max:999999999',
            'price_period'     => 'nullable|string|in:jour,semaine,mois,annee',
            'surface'          => 'nullable|numeric|min:1|max:99999',
            'rooms'            => 'nullable|integer|min:0|max:20',
            'bathrooms'        => 'nullable|integer|min:0|max:10',
            'floor'            => 'nullable|integer|min:0|max:100',
            'transaction_type' => 'required|in:' . implode(',', array_keys(Listing::TRANSACTION_TYPES)),
            'property_type'    => 'required|in:' . implode(',', array_keys(Listing::PROPERTY_TYPES)),
            'city'             => 'required|string',
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
            'is_sponsored_requested' => 'nullable|boolean',
            'images'           => "nullable|array|max:{$maxImages}",
            'images.*'         => 'image|mimes:jpg,jpeg,png,webp|max:5120',
            'image_urls'       => 'nullable|array',
            'image_urls.*'     => 'url',
        ]);

        $validated = $this->hydrateCoordinates($validated);
        
        if (!in_array($validated['transaction_type'], ['location', 'vacances'])) {
            $validated['price_period'] = null;
        }

        $validated['expires_at'] = now()->addDays($user->listing_duration_days);
        $validated['is_sponsored'] = false;
        $validated['sponsored_until'] = null;

        $requestedSponsor = $request->boolean('is_sponsored_requested') && $user->canCreateSponsoredListing();

        if ($requestedSponsor) {
            $validated['is_sponsored'] = true;
            $validated['sponsored_until'] = now()->addDays($user->sponsored_listing_duration_days);
        }

        unset($validated['is_sponsored_requested']);

        $listing = $user->listings()->create($validated);

        if ($requestedSponsor) {
            $listing->sponsorships()->create([
                'user_id' => $user->id,
                'type' => $user->preferred_sponsorship_type,
                'status' => 'pending',
                'starts_at' => null,
                'expires_at' => $listing->sponsored_until,
                'amount' => 0,
                'currency' => 'MAD',
                'duration_days' => $user->sponsored_listing_duration_days,
                'admin_notes' => 'Sponsorisation incluse via abonnement ' . $user->plan,
            ]);
        }

        $order = 0;
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                if ($order >= $maxImages) break;
                $path = $image->store("listings/{$listing->id}", 'public');
                $listing->images()->create(['path' => $path, 'order' => $order]);
                if ($order === 0) {
                    $listing->update(['thumbnail' => $path]);
                }
                $order++;
            }
        }

        if ($request->has('image_urls')) {
            foreach ($request->image_urls as $url) {
                if ($order >= $maxImages) break;
                try {
                    $response = \Illuminate\Support\Facades\Http::timeout(10)->get($url);
                    if ($response->successful()) {
                        // Attempt to extract extension from content type or url
                        $ext = 'jpg';
                        $contentType = $response->header('Content-Type');
                        if (str_contains($contentType, 'png')) $ext = 'png';
                        elseif (str_contains($contentType, 'webp')) $ext = 'webp';

                        $name = uniqid() . '.' . $ext;
                        $path = "listings/{$listing->id}/" . $name;
                        Storage::disk('public')->put($path, $response->body());
                        
                        $listing->images()->create(['path' => $path, 'order' => $order]);
                        if ($order === 0) {
                            $listing->update(['thumbnail' => $path]);
                        }
                        $order++;
                    }
                } catch (\Exception $e) {
                    // Ignore download failures
                }
            }
        }

        \App\Jobs\AiModerateListingJob::dispatch($listing)->onQueue('ai');
        $this->email->sendListingSubmitted($listing);

        return redirect()->route('user.listings.show', $listing)
            ->with('success', 'Annonce soumise ! Elle sera vérifiée par notre équipe sous 24h.');
    }

    public function show(Listing $listing)
    {
        $this->authorize('view', $listing);
        $listing->load('images');
        return view('pages.user.listings.show', compact('listing'));
    }

    public function edit(Listing $listing)
    {
        $this->authorize('update', $listing);
        $listing->load('images');

        return view('pages.user.listings.create', [
            'listing' => $listing,
            'cities' => Listing::CITIES,
            'propertyTypes' => Listing::PROPERTY_TYPES,
            'transactionTypes' => Listing::TRANSACTION_TYPES,
            'pricePeriods' => Listing::PRICE_PERIODS,
            'canCreateSponsoredListing' => auth()->user()->canCreateSponsoredListing(),
            'sponsoredDurationDays' => auth()->user()->sponsored_listing_duration_days,
            'listingDurationDays' => auth()->user()->listing_duration_days,
            'maxImages' => auth()->user()->max_images_per_ad,
        ]);
    }

    public function update(Request $request, Listing $listing)
    {
        $this->authorize('update', $listing);

        $validated = $request->validate([
            'title'       => 'required|string|min:10|max:150',
            'description' => 'required|string|min:50|max:5000',
            'price'       => 'required|numeric|min:1',
            'price_period'=> 'nullable|string|in:jour,semaine,mois,annee',
            'surface'     => 'nullable|numeric|min:1',
            'rooms'       => 'nullable|integer|min:0|max:20',
            'city'        => 'required|string',
            'zone'        => 'nullable|string|max:100',
            'address'     => 'nullable|string|max:255',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
            'furnished'   => 'boolean',
            'parking'     => 'boolean',
            'elevator'    => 'boolean',
            'pool'        => 'boolean',
            'garden'      => 'boolean',
            'terrace'     => 'boolean',
            'security'    => 'boolean',
            'is_sponsored_requested' => 'nullable|boolean',
        ]);

        $validated = $this->hydrateCoordinates($validated);
        
        if (!in_array($listing->transaction_type, ['location', 'vacances']) && !in_array($request->input('transaction_type'), ['location', 'vacances'])) {
             // In edit form, transaction_type might be disabled or read-only, we check model then request
             $transactionType = $request->input('transaction_type', $listing->transaction_type);
             if (!in_array($transactionType, ['location', 'vacances'])) {
                 $validated['price_period'] = null;
             }
        } elseif (isset($validated['transaction_type']) && !in_array($validated['transaction_type'], ['location', 'vacances'])) {
            $validated['price_period'] = null;
        }

        $requestedSponsor = $request->boolean('is_sponsored_requested') && auth()->user()->canCreateSponsoredListing();
        $validated['is_sponsored'] = $requestedSponsor;
        $validated['sponsored_until'] = $requestedSponsor ? now()->addDays(auth()->user()->sponsored_listing_duration_days) : null;
        unset($validated['is_sponsored_requested']);

        $listing->update($validated + ['status' => 'pending']);

        if ($requestedSponsor) {
            $listing->sponsorships()->updateOrCreate(
                ['listing_id' => $listing->id, 'user_id' => auth()->id(), 'status' => 'pending'],
                [
                    'type' => auth()->user()->preferred_sponsorship_type,
                    'starts_at' => null,
                    'expires_at' => $listing->sponsored_until,
                    'amount' => 0,
                    'currency' => 'MAD',
                    'duration_days' => auth()->user()->sponsored_listing_duration_days,
                    'admin_notes' => 'Sponsorisation incluse via abonnement ' . auth()->user()->plan,
                ]
            );
        }

        return redirect()->route('user.listings.show', $listing)
            ->with('success', 'Annonce modifiée et soumise à une nouvelle vérification.');
    }

    public function destroy(Listing $listing)
    {
        $this->authorize('delete', $listing);

        foreach ($listing->images as $img) {
            Storage::disk('public')->delete($img->path);
        }

        $listing->delete();

        return redirect()->route('user.dashboard')->with('success', 'Annonce supprimée.');
    }

    // ─── Favoris (AJAX, sans Livewire) ───────────────────────────────────────

    public function favorites()
    {
        $favorites = auth()->user()->favoriteListings()->with('images')->paginate(12);
        return view('pages.user.favorites', compact('favorites'));
    }

    public function toggleFavorite(Request $request, Listing $listing): JsonResponse
    {
        $user = auth()->user();
        $favorite = Favorite::where('user_id', $user->id)->where('listing_id', $listing->id)->first();

        if ($favorite) {
            $favorite->delete();
            $isFavorited = false;
        } else {
            Favorite::create(['user_id' => $user->id, 'listing_id' => $listing->id]);
            $isFavorited = true;
        }

        $count = $listing->favorites()->count();

        return response()->json([
            'favorited' => $isFavorited,
            'count' => $count,
        ]);
    }

    // ─── IA : générer une description ─────────────────────────────────────────

    public function aiGenerateDescription(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'            => 'required|string|min:5|max:150',
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

    public function geocodeAddress(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'city' => 'required|string|max:100',
            'zone' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
        ]);

        try {
            $locationParts = array_filter([
                $validated['address'] ?? null,
                $validated['zone'] ?? null,
            ]);
            $query = !empty($locationParts) ? implode(', ', $locationParts) : ($validated['zone'] ?? $validated['city']);
            $coords = $query ? $this->geo->geocode($query, $validated['city']) : null;

            if (!$coords) {
                $coords = $this->geo->getCityCoordinates($validated['city']);
                return response()->json([
                    'lat' => $coords['lat'],
                    'lng' => $coords['lng'],
                    'precision' => 'city',
                    'message' => 'Coordonnées de la ville utilisées en attendant une adresse plus précise.',
                ]);
            }

            return response()->json([
                'lat' => $coords['lat'],
                'lng' => $coords['lng'],
                'precision' => 'exact',
                'display' => $coords['display'] ?? null,
            ]);
        } catch (\Exception $e) {
            $coords = $this->geo->getCityCoordinates($validated['city']);
            return response()->json([
                'lat' => $coords['lat'],
                'lng' => $coords['lng'],
                'precision' => 'city',
                'message' => 'Service de localisation temporairement indisponible. Position de la ville appliquée.',
            ]);
        }
    }

    // ─── Plans & Paiement ─────────────────────────────────────────────────────

    public function plans()
    {
        $plans = $this->payments->getPlans();
        $currentPlan = auth()->user()->plan ?? 'gratuit';
        return view('pages.user.plans', compact('plans', 'currentPlan'));
    }

    public function checkoutForm(Request $request)
    {
        $planSlug = $request->query('plan');
        if (!$planSlug || $planSlug === 'gratuit') {
            return redirect()->route('tarifs')->withErrors(['plan' => 'Veuillez choisir un plan payant valide.']);
        }

        $plan = $this->payments->getPlan($planSlug);
        if (!$plan) {
            return redirect()->route('tarifs')->withErrors(['plan' => 'Plan introuvable.']);
        }

        $user = auth()->user();
        $currentPlan = $user->plan ?? 'gratuit';

        if ($currentPlan === $planSlug) {
            return redirect()->route('user.dashboard')->with('info', 'Vous etes deja abonne a ce plan.');
        }

        return view('pages.user.checkout', [
            'type' => 'subscription',
            'plan' => $plan,
            'planSlug' => $planSlug,
            'user' => $user,
            'listing' => null,
            'sponsorshipType' => null,
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate(['plan' => 'required|exists:subscription_plans,slug']);

        $user = auth()->user();
        $plan = $request->plan;

        if (!$this->payments->stripeIsConfigured()) {
            try {
                $this->payments->simulatePlan($user, $plan);
                $planName = ucfirst($plan);
                return redirect()->route('user.dashboard')
                    ->with('success', "Plan {$planName} activé avec succès ! (mode démo — paiement simulé)");
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error simulating plan: ' . $e->getMessage() . ' ' . $e->getTraceAsString());
                return back()->withErrors(['payment' => 'Erreur lors de l\'activation du plan. Réessayez.']);
            }
        }

        try {
            $result = $this->payments->createStripeSession($user, $plan);
            return redirect($result['url']);
        } catch (\Exception $e) {
            return back()->withErrors(['payment' => 'Erreur de paiement Stripe. Réessayez.']);
        }
    }

    public function paymentSuccess(Request $request)
    {
        $payment = $this->payments->handleSuccess($request->session_id);
        if ($payment) {
            return redirect()->route('user.dashboard')
                ->with('success', "Paiement confirmé ! Votre plan {$payment->plan} est actif.");
        }
        return redirect()->route('user.plans')->withErrors(['payment' => 'Paiement non confirmé.']);
    }

    public function subscription()
    {
        $user = auth()->user();
        $currentPlan = $user->plan ?? 'gratuit';
        $planData = $this->payments->getPlan($currentPlan);
        $allPlans = $this->payments->getPlans();

        $quota = $user->plan_quota;
        $listingsUsed = $user->listings()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $daysRemaining = null;
        $expiresAt = null;
        if ($user->plan_expires_at) {
            $expiresAt = $user->plan_expires_at;
            $daysRemaining = (int) max(0, now()->diffInDays($user->plan_expires_at, false));
        }

        $paymentHistory = \App\Models\Payment::where('user_id', $user->id)
            ->where('amount', '>', 0)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $sponsoredListings = $user->listings()
            ->where('is_sponsored', true)
            ->where('sponsored_until', '>', now())
            ->get();

        return view('pages.user.subscription', compact(
            'user',
            'currentPlan',
            'planData',
            'allPlans',
            'quota',
            'listingsUsed',
            'daysRemaining',
            'expiresAt',
            'paymentHistory',
            'sponsoredListings',
        ));
    }

    // ─── Sponsorisation des annonces ──────────────────────────────────────────

    public function sponsor(Listing $listing)
    {
        $this->authorize('view', $listing);

        if ($listing->status !== 'active') {
            return back()->withErrors(['sponsor' => 'Seules les annonces actives peuvent être sponsorisées.']);
        }

        if ($listing->isCurrentlySponsored()) {
            return back()->with('info', 'Cette annonce est déjà sponsorisée jusqu\'au ' . $listing->sponsored_until->format('d/m/Y') . '.');
        }

        return view('pages.user.listings.sponsor', [
            'listing' => $listing,
            'types' => Sponsorship::TYPES,
        ]);
    }

    public function sponsorCheckoutForm(Request $request, Listing $listing)
    {
        $this->authorize('view', $listing);

        $type = $request->query('type');
        if (!$type || !isset(Sponsorship::TYPES[$type])) {
            return redirect()->route('user.listings.sponsor', $listing)
                ->withErrors(['type' => 'Veuillez choisir un type de sponsorisation valide.']);
        }

        if ($listing->status !== 'active') {
            return back()->withErrors(['sponsor' => 'Seules les annonces actives peuvent etre sponsorisees.']);
        }

        if ($listing->isCurrentlySponsored()) {
            return back()->with('info', 'Cette annonce est deja sponsorisee.');
        }

        $sponsorshipType = Sponsorship::TYPES[$type];

        return view('pages.user.checkout', [
            'type' => 'sponsorship',
            'plan' => null,
            'planSlug' => null,
            'user' => auth()->user(),
            'listing' => $listing,
            'sponsorshipType' => $sponsorshipType,
            'sponsorshipKey' => $type,
        ]);
    }

    public function sponsorCheckout(Request $request, Listing $listing)
    {
        $this->authorize('view', $listing);

        $request->validate([
            'type' => 'required|in:basic,premium,premium_plus',
        ]);

        $user = auth()->user();
        $type = $request->type;

        if (!$this->payments->stripeIsConfigured()) {
            try {
                $typeLabel = Sponsorship::TYPES[$type]['label'] ?? $type;
                $this->payments->simulateSponsorship($user, $listing, $type);
                return redirect()->route('user.listings.show', $listing)
                    ->with('success', "Sponsorisation {$typeLabel} activée avec succès ! (mode démo)");
            } catch (\Exception $e) {
                return back()->withErrors(['sponsor' => 'Erreur lors de l\'activation de la sponsorisation.']);
            }
        }

        try {
            $result = $this->payments->createSponsorshipStripeSession($user, $listing, $type);
            return redirect($result['url']);
        } catch (\Exception $e) {
            return back()->withErrors(['sponsor' => 'Erreur de paiement. Réessayez.']);
        }
    }

    public function sponsorSuccess(Request $request)
    {
        $sponsorship = $this->payments->handleSponsorshipSuccess($request->session_id);

        if ($sponsorship) {
            return redirect()->route('user.listings.show', $sponsorship->listing_id)
                ->with('success', 'Sponsorisation activée ! Votre annonce bénéficie désormais d\'une visibilité accrue.');
        }

        return redirect()->route('user.dashboard')
            ->withErrors(['sponsor' => 'Sponsorisation non confirmée. Contactez le support si le problème persiste.']);
    }

    protected function hydrateCoordinates(array $validated): array
    {
        if (empty($validated['latitude'])) {
            $locationParts = array_filter([
                $validated['address'] ?? null,
                $validated['zone'] ?? null,
            ]);
            $locationQuery = !empty($locationParts) ? implode(', ', $locationParts) : ($validated['zone'] ?? $validated['city']);

            if (!empty($locationQuery)) {
                $coords = $this->geo->geocode($locationQuery, $validated['city']);
                if ($coords) {
                    $validated['latitude'] = $coords['lat'];
                    $validated['longitude'] = $coords['lng'];
                }
            }
        }

        if (empty($validated['latitude'])) {
            $coords = $this->geo->getCityCoordinates($validated['city']);
            $validated['latitude'] = $coords['lat'];
            $validated['longitude'] = $coords['lng'];
        }

        return $validated;
    }
}
