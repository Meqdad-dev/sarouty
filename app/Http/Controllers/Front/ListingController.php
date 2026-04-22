<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Message;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ListingController extends Controller
{
    /**
     * Page d'accueil avec annonces vedettes.
     */
    public function home()
    {
        // Mise en cache des annonces vedettes (15 minutes)
        $featuredListings = Cache::remember('featured_listings', 900, function () {
            return Listing::with(['images', 'user'])
                ->featured()
                ->ranked()
                ->take(8)
                ->get();
        });

        $recentListings = Listing::with(['images', 'user'])
            ->active()
            ->ranked()
            ->take(12)
            ->get();

        $stats = Cache::remember('home_stats', 3600, function () {
            return [
                'total'     => Listing::active()->count(),
                'vente'     => Listing::active()->byType('vente')->count(),
                'location'  => Listing::active()->byType('location')->count(),
                'cities'    => Listing::active()->distinct('city')->count('city'),
            ];
        });

        return view('pages.front.home', compact('featuredListings', 'recentListings', 'stats'));
    }

    /**
     * Liste des annonces avec filtres et pagination.
     */
    public function index(Request $request)
    {
        $query = Listing::with(['images', 'user'])->active();

        // Filtres de recherche
        if ($request->filled('q')) {
            $query->search($request->q);
        }
        if ($request->filled('city')) {
            $query->byCity($request->city);
        }
        if ($request->filled('type')) {
            $query->byType($request->type);
        }
        if ($request->filled('property')) {
            $query->byProperty($request->property);
        }
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->priceRange($request->min_price, $request->max_price);
        }
        if ($request->filled('min_surface')) {
            $query->where('surface', '>=', $request->min_surface);
        }
        if ($request->filled('rooms')) {
            $query->where('rooms', '>=', $request->rooms);
        }
        if ($request->boolean('furnished')) {
            $query->where('furnished', true);
        }
        if ($request->boolean('parking')) {
            $query->where('parking', true);
        }

        // Tri (ranking intelligent, puis tri utilisateur)
        $sort = $request->get('sort', 'latest');
        $query->ranked();
        match ($sort) {
            'price_asc'   => $query->orderBy('price', 'asc'),
            'price_desc'  => $query->orderBy('price', 'desc'),
            'surface_asc' => $query->orderBy('surface', 'asc'),
            'popular'     => $query->orderBy('views', 'desc'),
            default       => $query->latest(),
        };

        $listings = $query->paginate(12)->withQueryString();

        return view('pages.front.listings.index', [
            'listings'         => $listings,
            'cities'           => Listing::CITIES,
            'transactionTypes' => Listing::TRANSACTION_TYPES,
            'propertyTypes'    => Listing::PROPERTY_TYPES,
            'filters'          => $request->only([
                'q', 'city', 'type', 'property',
                'min_price', 'max_price', 'min_surface', 'rooms', 'sort',
                'furnished', 'parking',
            ]),
        ]);
    }

    /**
     * Page de détail d'une annonce.
     */
    public function show(Listing $listing)
    {
        // Seules les annonces actives sont visibles par le public
        // (les propriétaires et admins peuvent voir leurs propres annonces)
        if ($listing->status !== 'active') {
            if (!auth()->check()) abort(404);
            $user = auth()->user();
            if (!$user->isAdmin() && $listing->user_id !== $user->id) {
                abort(404);
            }
        }

        $listing->load(['images', 'user']);
        $listing->incrementViews();

        // Annonces similaires
        $similarListings = Listing::with('images')
            ->active()
            ->where('id', '!=', $listing->id)
            ->where('city', $listing->city)
            ->where('transaction_type', $listing->transaction_type)
            ->take(4)
            ->get();

        $isFavorited = $listing->isFavoritedBy(auth()->user());

        return view('pages.front.listings.show', compact('listing', 'similarListings', 'isFavorited'));
    }

    /**
     * Envoie un message de contact pour une annonce.
     */
    public function contact(Request $request, Listing $listing)
    {
        $validated = $request->validate([
            'sender_name'  => 'required|string|max:100',
            'sender_email' => 'required|email|max:150',
            'sender_phone' => 'nullable|string|max:20',
            'message'      => 'required|string|min:10|max:1000',
        ]);

        Message::create([
            'listing_id'   => $listing->id,
            'sender_id'    => auth()->id(),
            'receiver_id'  => $listing->user_id,
            'sender_name'  => $validated['sender_name'],
            'sender_email' => $validated['sender_email'],
            'sender_phone' => $validated['sender_phone'] ?? null,
            'message'      => $validated['message'],
            'status'       => 'pending',
        ]);

        return back()->with('success', 'Votre message a été envoyé avec succès. Il est en attente de modération par l\'administration.');
    }

    /**
     * Signaler une annonce.
     */
    public function report(Request $request, Listing $listing)
    {
        $validated = $request->validate([
            'reason'         => 'required|in:' . implode(',', array_keys(Report::REASONS)),
            'description'    => 'nullable|string|max:500',
        ]);

        $report = Report::create([
            'listing_id'     => $listing->id,
            'user_id'        => auth()->id(),
            'reporter_email' => auth()->user()->email,
            'reason'         => $validated['reason'],
            'description'    => $validated['description'] ?? null,
        ]);

        return back()->with('success', 'Votre signalement a été enregistré. Merci pour votre vigilance.');
    }
}
