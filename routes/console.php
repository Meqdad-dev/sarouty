<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Listing;
use App\Models\Sponsorship;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ─── Desactiver les abonnements expires ─────────────────────────────────────
Artisan::command('subscriptions:expire', function () {
    $expired = User::where('plan', '!=', 'gratuit')
        ->whereNotNull('plan_expires_at')
        ->where('plan_expires_at', '<=', now())
        ->get();

    $count = 0;
    foreach ($expired as $user) {
        $user->downgradeToFree();
        $count++;
    }

    $this->info("Abonnements expires desactives : {$count}");
    if ($count > 0) {
        Log::info("Scheduler: {$count} abonnement(s) expire(s) desactive(s).");
    }
})->purpose('Desactiver les abonnements expires');

// ─── Desactiver les sponsorisations expirees ────────────────────────────────
Artisan::command('sponsorships:expire', function () {
    // Expire sponsorship records
    $expiredSponsorships = Sponsorship::where('status', 'active')
        ->where('expires_at', '<=', now())
        ->get();

    $count = 0;
    foreach ($expiredSponsorships as $sponsorship) {
        $sponsorship->markExpired();
        $count++;
    }

    // Deactivate listing-level sponsorship flags
    $expiredListings = Listing::where('is_sponsored', true)
        ->where('sponsored_until', '<=', now())
        ->get();

    $listingCount = 0;
    foreach ($expiredListings as $listing) {
        $listing->deactivateSponsorship();
        $listingCount++;
    }

    $this->info("Sponsorisations expirees : {$count} enregistrement(s), {$listingCount} annonce(s)");
    if ($count > 0 || $listingCount > 0) {
        Log::info("Scheduler: {$count} sponsorisation(s) et {$listingCount} annonce(s) desactivee(s).");
    }
})->purpose('Desactiver les sponsorisations expirees');

// ─── Programmation des taches ────────────────────────────────────────────────
Schedule::command('subscriptions:expire')->dailyAt('02:00');
Schedule::command('sponsorships:expire')->hourly();
