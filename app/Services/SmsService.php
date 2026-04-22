<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client as TwilioClient;

class SmsService
{
    protected ?TwilioClient $twilio = null;

    public function __construct()
    {
        if (config('services.twilio.sid') && config('services.twilio.token')) {
            $this->twilio = new TwilioClient(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );
        }
    }

    /**
     * Envoie un SMS via Twilio ou Infobip.
     */
    public function send(string $to, string $message): bool
    {
        // Normalise le numéro marocain
        $to = $this->normalizePhone($to);

        if ($this->twilio) {
            return $this->sendViaTwilio($to, $message);
        }

        if (config('services.infobip.api_key')) {
            return $this->sendViaInfobip($to, $message);
        }

        Log::warning('SmsService: aucun provider SMS configuré.');
        return false;
    }

    /**
     * SMS de confirmation d'inscription.
     */
    public function sendWelcome(string $phone, string $name): bool
    {
        return $this->send($phone, "Bienvenue sur Sarouty, {$name} ! 🏠 Votre compte a été créé avec succès. Découvrez des milliers d'annonces immobilières au Maroc sur sarouty.ma");
    }

    /**
     * SMS quand une annonce est approuvée.
     */
    public function sendListingApproved(string $phone, string $listingTitle): bool
    {
        return $this->send($phone, "✅ Sarouty: Votre annonce \"{$listingTitle}\" a été approuvée et est maintenant visible par tous les visiteurs. Bonne chance pour votre vente !");
    }

    /**
     * SMS quand une annonce est refusée.
     */
    public function sendListingRejected(string $phone, string $listingTitle, string $reason): bool
    {
        return $this->send($phone, "❌ Sarouty: Votre annonce \"{$listingTitle}\" n'a pas été approuvée. Motif: {$reason}. Connectez-vous pour modifier et resoumettre.");
    }

    /**
     * SMS de nouveau message reçu.
     */
    public function sendNewMessage(string $phone, string $senderName, string $listingTitle): bool
    {
        return $this->send($phone, "💬 Sarouty: {$senderName} vous a envoyé un message concernant \"{$listingTitle}\". Connectez-vous pour répondre sur sarouty.ma");
    }

    /**
     * OTP / Code de vérification.
     */
    public function sendOtp(string $phone, string $code): bool
    {
        return $this->send($phone, "Sarouty: Votre code de vérification est {$code}. Valable 10 minutes. Ne le partagez jamais.");
    }

    // ─── Providers ────────────────────────────────────────────────────────────

    private function sendViaTwilio(string $to, string $message): bool
    {
        try {
            $this->twilio->messages->create($to, [
                'from' => config('services.twilio.from'),
                'body' => $message,
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Twilio SMS error: ' . $e->getMessage(), ['to' => $to]);
            return false;
        }
    }

    private function sendViaInfobip(string $to, string $message): bool
    {
        try {
            $baseUrl = config('services.infobip.base_url');
            $apiKey  = config('services.infobip.api_key');
            $from    = config('services.infobip.from', 'Sarouty');

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => "App {$apiKey}",
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ])->post("{$baseUrl}/sms/2/text/advanced", [
                'messages' => [[
                    'from'         => $from,
                    'destinations' => [['to' => $to]],
                    'text'         => $message,
                ]],
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Infobip SMS error: ' . $e->getMessage(), ['to' => $to]);
            return false;
        }
    }

    private function normalizePhone(string $phone): string
    {
        // Supprime espaces et tirets
        $phone = preg_replace('/[\s\-\(\)]/', '', $phone);

        // Convertit format marocain local en international
        if (str_starts_with($phone, '06') || str_starts_with($phone, '07')) {
            $phone = '+212' . substr($phone, 1);
        } elseif (str_starts_with($phone, '212')) {
            $phone = '+' . $phone;
        }

        return $phone;
    }
}
