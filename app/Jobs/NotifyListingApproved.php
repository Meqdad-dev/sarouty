<?php

namespace App\Jobs;

use App\Models\Listing;
use App\Models\User;
use App\Services\SmsService;
use App\Services\EmailService;
use App\Services\AiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

// ─── Job : Notifier l'approbation d'une annonce ──────────────────────────────

class NotifyListingApproved implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(public Listing $listing) {}

    public function handle(SmsService $sms, EmailService $email): void
    {
        $user = $this->listing->user;

        // Email
        $email->sendListingApproved($this->listing);

        // SMS si numéro disponible
        if ($user->phone) {
            $sms->sendListingApproved($user->phone, $this->listing->title);
        }

        Log::info("Listing approved notifications sent", ['listing_id' => $this->listing->id]);
    }

    public function tags(): array
    {
        return ['notifications', 'listing:' . $this->listing->id];
    }
}
