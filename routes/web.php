<?php

use App\Http\Controllers\Front\ListingController;
use App\Http\Controllers\Front\EstimationController;
use App\Http\Controllers\User\UserListingController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes publiques
|--------------------------------------------------------------------------
*/

Route::get('/', [ListingController::class, 'home'])->name('home');

// Estimation de bien (accessible à tous)
Route::post('/estimation', [EstimationController::class, 'store'])->name('estimation.store');

// Page tarifs publique
Route::get('/tarifs', fn() => view('pages.front.tarifs'))->name('tarifs');

Route::prefix('annonces')->name('listings.')->group(function () {
    Route::get('/',                    [ListingController::class, 'index'])->name('index');
    Route::get('/{listing}',           [ListingController::class, 'show'])->name('show');
    Route::post('/{listing}/contact',  [ListingController::class, 'contact'])->name('contact')->middleware('auth');
    Route::post('/{listing}/signaler', [ListingController::class, 'report'])->name('report')->middleware('auth');
});

// API JSON pour la recherche (remplace Livewire SearchListings)
Route::get('/api/listings/search', [ListingController::class, 'searchApi'])->name('listings.search.api');

// API settings publics
Route::get('/api/settings', [\App\Http\Controllers\Admin\SettingController::class, 'public'])->name('api.settings.public');

// API suggestions IA de recherche
Route::get('/api/ai/search-suggestions', function (\Illuminate\Http\Request $request) {
    $query       = $request->input('q', '');
    $suggestions = app(\App\Services\AiService::class)->generateSearchSuggestions($query);
    return response()->json(['suggestions' => $suggestions]);
})->middleware('throttle:20,1')->name('api.ai.suggestions');

/*
|--------------------------------------------------------------------------
| Auth (Laravel Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Espace utilisateur
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('mon-compte')->name('user.')->group(function () {

    Route::get('/tableau-de-bord', [UserListingController::class, 'dashboard'])->name('dashboard');

    Route::get('/profil', [UserListingController::class, 'profile'])->name('profile');
    Route::put('/profil', [UserListingController::class, 'updateProfile'])->name('profile.update');

    // CRUD annonces
    Route::prefix('annonces')->name('listings.')->group(function () {
        Route::get('/creer',              [UserListingController::class, 'create'])->name('create');
        Route::post('/',                  [UserListingController::class, 'store'])->name('store');
        Route::get('/{listing}',          [UserListingController::class, 'show'])->name('show');
        Route::get('/{listing}/modifier', [UserListingController::class, 'edit'])->name('edit');
        Route::put('/{listing}',          [UserListingController::class, 'update'])->name('update');
        Route::delete('/{listing}',       [UserListingController::class, 'destroy'])->name('destroy');
    });

    // Favoris (AJAX, sans Livewire)
    Route::get('/favoris',                       [UserListingController::class, 'favorites'])->name('favorites');
    Route::post('/favoris/{listing}/toggle',     [UserListingController::class, 'toggleFavorite'])->name('favorites.toggle');

    // Messages
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/',                          [\App\Http\Controllers\User\UserMessageController::class, 'index'])->name('index');
        Route::get('/creer/{listing}',           [\App\Http\Controllers\User\UserMessageController::class, 'create'])->name('create');
        Route::post('/',                          [\App\Http\Controllers\User\UserMessageController::class, 'store'])->name('store');
        Route::get('/{message}',                 [\App\Http\Controllers\User\UserMessageController::class, 'show'])->name('show');
        Route::post('/{message}/repondre',       [\App\Http\Controllers\User\UserMessageController::class, 'reply'])->name('reply');
        Route::post('/{message}/lu',             [\App\Http\Controllers\User\UserMessageController::class, 'markRead'])->name('read');
        Route::delete('/{message}',              [\App\Http\Controllers\User\UserMessageController::class, 'destroy'])->name('destroy');
        Route::get('/api/non-lus',               [\App\Http\Controllers\User\UserMessageController::class, 'unreadCount'])->name('unread');
    });

    // IA : générer une description
    Route::post('/ai/generate-description', [UserListingController::class, 'aiGenerateDescription'])
        ->name('ai.generate-description')
        ->middleware('throttle:10,1');

    Route::post('/ai/geocode-address', [UserListingController::class, 'geocodeAddress'])
        ->name('ai.geocode-address')
        ->middleware('throttle:20,1');

    // Plans & Paiement
    Route::get('/abonnement',         [UserListingController::class, 'subscription'])->name('subscription');
    Route::get('/checkout',           [UserListingController::class, 'checkoutForm'])->name('checkout.form');
    Route::post('/checkout',          [UserListingController::class, 'checkout'])->name('checkout');
    Route::get('/paiement/succes',    [UserListingController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/paiement/annule',    fn() => redirect()->route('tarifs')->with('info', 'Paiement annule.'))->name('payment.cancel');

    // Sponsorisation des annonces
    Route::get('/annonces/{listing}/sponsoriser',              [UserListingController::class, 'sponsor'])->name('listings.sponsor');
    Route::get('/annonces/{listing}/sponsoriser/paiement',     [UserListingController::class, 'sponsorCheckoutForm'])->name('listings.sponsor.checkout.form');
    Route::post('/annonces/{listing}/sponsoriser',             [UserListingController::class, 'sponsorCheckout'])->name('listings.sponsor.checkout');
    Route::get('/sponsorisation/succes',                       [UserListingController::class, 'sponsorSuccess'])->name('sponsor.success');

    // Notifications
    Route::get('/notifications',                        [\App\Http\Controllers\User\NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/{notification}/read',   [\App\Http\Controllers\User\NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all',              [\App\Http\Controllers\User\NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::delete('/notifications/{notification}',          [\App\Http\Controllers\User\NotificationController::class, 'destroy'])->name('notifications.destroy');
});

/*
|--------------------------------------------------------------------------
| Administration
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/tableau-de-bord', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/profil', [AdminController::class, 'profile'])->name('profile');
    Route::put('/profil', [AdminController::class, 'updateProfile'])->name('profile.update');

    // API stats pour graphiques AJAX
    Route::get('/api/stats', [AdminController::class, 'statsApi'])->name('api.stats');

    // Annonces
    Route::prefix('annonces')->name('listings.')->group(function () {
        Route::get('/',                          [AdminController::class, 'listingsIndex'])->name('index');
        Route::get('/creer',                     [AdminController::class, 'listingCreate'])->name('create');
        Route::post('/',                         [AdminController::class, 'listingStore'])->name('store');
        Route::post('/ai/generate-description',  [AdminController::class, 'aiGenerateDescription'])->name('ai.generate-description')->middleware('throttle:10,1');
        Route::get('/{listing}',                 [AdminController::class, 'listingShow'])->name('show');
        Route::get('/{listing}/modifier',        [AdminController::class, 'listingEdit'])->name('edit');
        Route::put('/{listing}',                 [AdminController::class, 'listingUpdate'])->name('update');
        Route::post('/{listing}/statut',         [AdminController::class, 'listingUpdateStatus'])->name('update-status');
        Route::post('/{listing}/priorite',       [AdminController::class, 'listingSetPriority'])->name('set-priority');
        Route::post('/{listing}/valider',        [AdminController::class, 'listingApprove'])->name('approve');
        Route::post('/{listing}/refuser',        [AdminController::class, 'listingReject'])->name('reject');
        Route::post('/{listing}/mise-en-avant',  [AdminController::class, 'listingFeature'])->name('feature');
        Route::delete('/{listing}',              [AdminController::class, 'listingDestroy'])->name('destroy');
    });

    // Utilisateurs
    Route::prefix('utilisateurs')->name('users.')->group(function () {
        Route::get('/',                 [AdminController::class, 'usersIndex'])->name('index');
        Route::get('/creer',            [AdminController::class, 'userCreate'])->name('create');
        Route::post('/',                [AdminController::class, 'userStore'])->name('store');
        Route::get('/{user}',           [AdminController::class, 'userShow'])->name('show');
        Route::get('/{user}/modifier',  [AdminController::class, 'userEdit'])->name('edit');
        Route::put('/{user}',           [AdminController::class, 'userUpdate'])->name('update');
        Route::post('/{user}/activer',  [AdminController::class, 'userToggleActive'])->name('toggle-active');
        Route::post('/{user}/bannir',   [AdminController::class, 'userBan'])->name('ban');
        Route::post('/{user}/debannir', [AdminController::class, 'userUnban'])->name('unban');
        Route::delete('/{user}',        [AdminController::class, 'userDestroy'])->name('destroy');
    });

    // Signalements
    Route::prefix('signalements')->name('reports.')->group(function () {
        Route::get('/',                           [AdminController::class, 'reportsIndex'])->name('index');
        Route::get('/{report}',                   [AdminController::class, 'reportShow'])->name('show');
        Route::post('/{report}/resoudre',         [AdminController::class, 'reportResolve'])->name('resolve');
        Route::post('/{report}/rejeter',          [AdminController::class, 'reportDismiss'])->name('dismiss');
        Route::post('/{report}/transferer',       [AdminController::class, 'reportForward'])->name('forward');
        Route::post('/{report}/supprimer-annonce',[AdminController::class, 'reportDeleteListing'])->name('delete-listing');
        Route::post('/{report}/bannir-utilisateur',[AdminController::class, 'reportBanUser'])->name('ban-user');
    });

    // Sponsorisations
    Route::prefix('sponsorisations')->name('sponsorships.')->group(function () {
        Route::get('/',                      [\App\Http\Controllers\Admin\SponsorshipController::class, 'index'])->name('index');
        Route::get('/creer',                 [\App\Http\Controllers\Admin\SponsorshipController::class, 'create'])->name('create');
        Route::post('/',                     [\App\Http\Controllers\Admin\SponsorshipController::class, 'store'])->name('store');
        Route::get('/{sponsorship}',         [\App\Http\Controllers\Admin\SponsorshipController::class, 'show'])->name('show');
        Route::get('/{sponsorship}/modifier',[\App\Http\Controllers\Admin\SponsorshipController::class, 'edit'])->name('edit');
        Route::put('/{sponsorship}',         [\App\Http\Controllers\Admin\SponsorshipController::class, 'update'])->name('update');
        Route::post('/{sponsorship}/activer',[\App\Http\Controllers\Admin\SponsorshipController::class, 'activate'])->name('activate');
        Route::post('/{sponsorship}/pause',  [\App\Http\Controllers\Admin\SponsorshipController::class, 'pause'])->name('pause');
        Route::post('/{sponsorship}/reprendre',[\App\Http\Controllers\Admin\SponsorshipController::class, 'resume'])->name('resume');
        Route::post('/{sponsorship}/approuver-annonce',[\App\Http\Controllers\Admin\SponsorshipController::class, 'approveListing'])->name('approve-listing');
        Route::post('/{sponsorship}/refuser-annonce',[\App\Http\Controllers\Admin\SponsorshipController::class, 'rejectListing'])->name('reject-listing');
        Route::post('/{sponsorship}/annuler',[\App\Http\Controllers\Admin\SponsorshipController::class, 'cancel'])->name('cancel');
        Route::delete('/{sponsorship}',      [\App\Http\Controllers\Admin\SponsorshipController::class, 'destroy'])->name('destroy');
    });

    // Plans d'abonnement
    Route::resource('plans', \App\Http\Controllers\Admin\SubscriptionPlanController::class);

    // Abonnements
    Route::prefix('abonnements')->name('subscriptions.')->group(function () {
        Route::get('/',                      [\App\Http\Controllers\Admin\SubscriptionController::class, 'index'])->name('index');
        Route::get('/creer',                 [\App\Http\Controllers\Admin\SubscriptionController::class, 'create'])->name('create');
        Route::post('/',                     [\App\Http\Controllers\Admin\SubscriptionController::class, 'store'])->name('store');
        Route::get('/{payment}',             [\App\Http\Controllers\Admin\SubscriptionController::class, 'show'])->name('show');
        Route::get('/{payment}/modifier',    [\App\Http\Controllers\Admin\SubscriptionController::class, 'edit'])->name('edit');
        Route::put('/{payment}',             [\App\Http\Controllers\Admin\SubscriptionController::class, 'update'])->name('update');
        Route::post('/{payment}/prolonger',  [\App\Http\Controllers\Admin\SubscriptionController::class, 'extend'])->name('extend');
        Route::post('/{payment}/annuler',    [\App\Http\Controllers\Admin\SubscriptionController::class, 'cancel'])->name('cancel');
    });

    // Paiements
    Route::prefix('paiements')->name('payments.')->group(function () {
        Route::get('/',                      [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('index');
        Route::get('/export',                [\App\Http\Controllers\Admin\PaymentController::class, 'export'])->name('export');
        Route::get('/{payment}',             [\App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('show');
        Route::post('/{payment}/rembourser', [\App\Http\Controllers\Admin\PaymentController::class, 'refund'])->name('refund');
    });

    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\MessageController::class, 'index'])->name('index');
        Route::post('/{message}/approve', [\App\Http\Controllers\Admin\MessageController::class, 'approve'])->name('approve');
        Route::post('/{message}/reject', [\App\Http\Controllers\Admin\MessageController::class, 'reject'])->name('reject');
        Route::post('/{message}/read', [\App\Http\Controllers\Admin\MessageController::class, 'markRead'])->name('read');
        Route::post('/{message}/reply', [\App\Http\Controllers\Admin\MessageController::class, 'reply'])->name('reply');
        Route::delete('/{message}', [\App\Http\Controllers\Admin\MessageController::class, 'destroy'])->name('destroy');
    });

    // Paramètres
    Route::prefix('parametres')->name('settings.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('update');
        Route::post('/vider-cache', [\App\Http\Controllers\Admin\SettingController::class, 'clearCache'])->name('clear-cache');
    });

    // Estimations
    Route::prefix('estimations')->name('estimations.')->group(function () {
        Route::get('/',                                [\App\Http\Controllers\Admin\EstimationController::class, 'index'])->name('index');
        Route::get('/{estimation}',                    [\App\Http\Controllers\Admin\EstimationController::class, 'show'])->name('show');
        Route::delete('/{estimation}',                 [\App\Http\Controllers\Admin\EstimationController::class, 'destroy'])->name('destroy');
        Route::post('/{estimation}/envoyer-email',     [\App\Http\Controllers\Admin\EstimationController::class, 'sendEmail'])->name('send-email');
        Route::post('/envoyer-email-masse',            [\App\Http\Controllers\Admin\EstimationController::class, 'sendBulkEmail'])->name('send-bulk-email');
    });
});
