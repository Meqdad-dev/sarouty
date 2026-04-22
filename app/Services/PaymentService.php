<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\User;
use App\Models\Listing;
use App\Models\Sponsorship;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PaymentService
{
    /**
     * Crée une session de paiement Stripe.
     */
    public function createStripeSession(User $user, string $planSlug): array
    {
        try {
            $secret = config('services.stripe.secret');
            if (empty($secret)) {
                throw new \RuntimeException('La clé Stripe STRIPE_SECRET est manquante.');
            }

            \Stripe\Stripe::setApiKey($secret);

            $plan = $this->getPlan($planSlug);
            if (!$plan || empty($plan->price) || ($plan->price ?? 0) <= 0) {
                throw new \InvalidArgumentException("Plan de paiement invalide : {$planSlug}");
            }

            $session = \Stripe\Checkout\Session::create([
                'mode' => 'payment',
                'payment_method_types' => ['card'],
                'customer_email' => $user->email,
                'metadata' => [
                    'user_id' => (string) $user->id,
                    'plan' => $planSlug,
                ],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'mad',
                        'unit_amount' => (int) round($plan->price * 100),
                        'product_data' => [
                            'name' => "Sarouty – {$plan->name}",
                            'description' => $plan->name . ' - Abonnement',
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'success_url' => route('user.payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('user.payment.cancel'),
            ]);

            return [
                'url' => $session->url,
                'session_id' => $session->id,
            ];
        } catch (\Throwable $e) {
            Log::error('Stripe session error', [
                'message' => $e->getMessage(),
                'user_id' => $user->id,
                'plan' => $planSlug,
            ]);

            throw $e;
        }
    }

    /**
     * Vérifie et traite un paiement réussi.
     */
    public function handleSuccess(?string $sessionId): ?Payment
    {
        if (empty($sessionId)) {
            Log::warning('Payment success called without session_id');
            return null;
        }

        try {
            $secret = config('services.stripe.secret');
            if (empty($secret)) {
                throw new \RuntimeException('La clé Stripe STRIPE_SECRET est manquante.');
            }

            \Stripe\Stripe::setApiKey($secret);
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            if (($session->payment_status ?? null) !== 'paid') {
                Log::warning('Stripe session not paid', [
                    'session_id' => $sessionId,
                    'payment_status' => $session->payment_status ?? null,
                ]);
                return null;
            }

            $userId = (int) ($session->metadata->user_id ?? 0);
            $plan = (string) ($session->metadata->plan ?? '');
            $planDef = $this->getPlan($plan);

            if (!$userId || !$planDef) {
                Log::error('Stripe session metadata invalid', [
                    'session_id' => $sessionId,
                    'user_id' => $userId,
                    'plan' => $plan,
                ]);
                return null;
            }

            $payment = Payment::firstOrCreate(
                ['stripe_session_id' => $sessionId],
                [
                    'user_id' => $userId,
                    'stripe_payment_intent' => is_string($session->payment_intent ?? null) ? $session->payment_intent : null,
                    'plan' => $plan,
                    'amount' => ($session->amount_total ?? 0) / 100,
                    'currency' => strtoupper($session->currency ?? 'MAD'),
                    'status' => 'completed',
                    'expires_at' => !empty($planDef->duration_days) ? now()->addDays((int) $planDef->duration_days) : null,
                ]
            );

            $payment->update([
                'status' => 'completed',
                'stripe_payment_intent' => is_string($session->payment_intent ?? null) ? $session->payment_intent : $payment->stripe_payment_intent,
            ]);

            $payment->user?->upgradeToPlan($plan, (int) ($planDef->duration_days ?? 30));
            Cache::forget('admin_dashboard_stats');
            Cache::forget('payment_stats');

            return $payment->fresh();
        } catch (\Throwable $e) {
            Log::error('Payment handle success error', [
                'message' => $e->getMessage(),
                'session_id' => $sessionId,
            ]);
            return null;
        }
    }

    /**
     * Liste des plans disponibles.
     */
    public function getPlans()
    {
        return Cache::remember('subscription_plans_all', 60*60*24, function () {
            return \App\Models\SubscriptionPlan::all()->keyBy('slug');
        });
    }

    public function getPlan(string $slug)
    {
        return $this->getPlans()->get($slug);
    }

    /**
     * Returns true only when real (non-placeholder) Stripe keys are configured.
     */
    public function stripeIsConfigured(): bool
    {
        $secret = config('services.stripe.secret', '');
        return !empty($secret)
            && !str_contains($secret, 'your_stripe')
            && str_starts_with($secret, 'sk_');
    }

    /**
     * Simulates a completed payment locally (dev/demo mode — no Stripe call).
     * Activates the plan directly on the user and records a local Payment row.
     */
    public function simulatePlan(User $user, string $planSlug): Payment
    {
        $plan = $this->getPlan($planSlug);

        $payment = Payment::create([
            'user_id'               => $user->id,
            'stripe_session_id'     => null,
            'stripe_payment_intent' => null,
            'plan'                  => $planSlug,
            'amount'                => $plan->price ?? 0,
            'currency'              => 'MAD',
            'status'                => 'completed',
            'expires_at'            => now()->addDays((int) ($plan->duration_days ?? 30)),
        ]);

        $user->upgradeToPlan($planSlug, (int) ($plan->duration_days ?? 30));
        Cache::forget('admin_dashboard_stats');
        Cache::forget('payment_stats');

        Log::info('Plan active en mode demo (sans Stripe)', [
            'user_id' => $user->id,
            'plan'    => $planSlug,
        ]);

        return $payment;
    }

    // ─── Sponsorisation ─────────────────────────────────────────────────────

    /**
     * Cree une session Stripe pour la sponsorisation d'une annonce.
     */
    public function createSponsorshipStripeSession(User $user, Listing $listing, string $type): array
    {
        try {
            $secret = config('services.stripe.secret');
            if (empty($secret)) {
                throw new \RuntimeException('La cle Stripe STRIPE_SECRET est manquante.');
            }

            \Stripe\Stripe::setApiKey($secret);

            $sponsorshipType = Sponsorship::TYPES[$type] ?? null;
            if (!$sponsorshipType) {
                throw new \InvalidArgumentException("Type de sponsorisation invalide : {$type}");
            }

            $session = \Stripe\Checkout\Session::create([
                'mode' => 'payment',
                'payment_method_types' => ['card'],
                'customer_email' => $user->email,
                'metadata' => [
                    'user_id' => (string) $user->id,
                    'listing_id' => (string) $listing->id,
                    'sponsorship_type' => $type,
                    'payment_type' => 'sponsorship',
                ],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'mad',
                        'unit_amount' => (int) round($sponsorshipType['price'] * 100),
                        'product_data' => [
                            'name' => "Sarouty - Sponsorisation {$sponsorshipType['label']}",
                            'description' => "Sponsorisation {$sponsorshipType['days']} jours pour : {$listing->title}",
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'success_url' => route('user.sponsor.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('user.listings.show', $listing) . '?cancelled=1',
            ]);

            return [
                'url' => $session->url,
                'session_id' => $session->id,
            ];
        } catch (\Throwable $e) {
            Log::error('Stripe sponsorship session error', [
                'message' => $e->getMessage(),
                'user_id' => $user->id,
                'listing_id' => $listing->id,
                'type' => $type,
            ]);
            throw $e;
        }
    }

    /**
     * Traite un paiement de sponsorisation reussi.
     */
    public function handleSponsorshipSuccess(?string $sessionId): ?Sponsorship
    {
        if (empty($sessionId)) {
            Log::warning('Sponsorship success called without session_id');
            return null;
        }

        try {
            $secret = config('services.stripe.secret');
            if (empty($secret)) {
                throw new \RuntimeException('La cle Stripe STRIPE_SECRET est manquante.');
            }

            \Stripe\Stripe::setApiKey($secret);
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            if (($session->payment_status ?? null) !== 'paid') {
                Log::warning('Stripe sponsorship session not paid', [
                    'session_id' => $sessionId,
                ]);
                return null;
            }

            $userId = (int) ($session->metadata->user_id ?? 0);
            $listingId = (int) ($session->metadata->listing_id ?? 0);
            $type = (string) ($session->metadata->sponsorship_type ?? '');
            $sponsorshipType = Sponsorship::TYPES[$type] ?? null;

            if (!$userId || !$listingId || !$sponsorshipType) {
                Log::error('Stripe sponsorship metadata invalid', [
                    'session_id' => $sessionId,
                ]);
                return null;
            }

            $listing = Listing::find($listingId);
            if (!$listing) {
                return null;
            }

            // Create payment record
            $payment = Payment::create([
                'user_id' => $userId,
                'stripe_session_id' => $sessionId,
                'stripe_payment_intent' => is_string($session->payment_intent ?? null) ? $session->payment_intent : null,
                'plan' => 'gratuit',
                'amount' => ($session->amount_total ?? 0) / 100,
                'currency' => strtoupper($session->currency ?? 'MAD'),
                'status' => 'completed',
                'expires_at' => now()->addDays($sponsorshipType['days']),
            ]);

            // Create sponsorship record
            $sponsorship = Sponsorship::create([
                'listing_id' => $listingId,
                'user_id' => $userId,
                'payment_id' => $payment->id,
                'type' => $type,
                'status' => 'active',
                'starts_at' => now(),
                'expires_at' => now()->addDays($sponsorshipType['days']),
                'amount' => $sponsorshipType['price'],
                'currency' => 'MAD',
                'duration_days' => $sponsorshipType['days'],
            ]);

            // Activate sponsorship on the listing
            $listing->activateSponsorship($sponsorshipType['days']);
            Cache::forget('admin_dashboard_stats');
            Cache::forget('payment_stats');
            Cache::forget('featured_listings');

            return $sponsorship;
        } catch (\Throwable $e) {
            Log::error('Sponsorship handle success error', [
                'message' => $e->getMessage(),
                'session_id' => $sessionId,
            ]);
            return null;
        }
    }

    /**
     * Simule une sponsorisation en mode demo (sans Stripe).
     */
    public function simulateSponsorship(User $user, Listing $listing, string $type): Sponsorship
    {
        $sponsorshipType = Sponsorship::TYPES[$type] ?? Sponsorship::TYPES['basic'];

        $payment = Payment::create([
            'user_id' => $user->id,
            'plan' => 'gratuit',
            'amount' => $sponsorshipType['price'],
            'currency' => 'MAD',
            'status' => 'completed',
            'expires_at' => now()->addDays($sponsorshipType['days']),
        ]);

        $sponsorship = Sponsorship::create([
            'listing_id' => $listing->id,
            'user_id' => $user->id,
            'payment_id' => $payment->id,
            'type' => $type,
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addDays($sponsorshipType['days']),
            'amount' => $sponsorshipType['price'],
            'currency' => 'MAD',
            'duration_days' => $sponsorshipType['days'],
        ]);

        $listing->activateSponsorship($sponsorshipType['days']);
        Cache::forget('admin_dashboard_stats');
        Cache::forget('payment_stats');
        Cache::forget('featured_listings');

        Log::info('Sponsorisation activee en mode demo', [
            'user_id' => $user->id,
            'listing_id' => $listing->id,
            'type' => $type,
        ]);

        return $sponsorship;
    }
}
