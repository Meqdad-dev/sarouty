<?php

namespace App\Jobs;

use App\Models\Listing;
use App\Services\SmsService;
use App\Services\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotifyListingRejected implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(public Listing $listing) {}

    public function handle(SmsService $sms, EmailService $email): void
    {
        $user = $this->listing->user;

        $email->sendListingRejected($this->listing);

        if ($user->phone && $this->listing->rejection_reason) {
            $sms->sendListingRejected($user->phone, $this->listing->title, $this->listing->rejection_reason);
        }

        Log::info("Listing rejected notifications sent", ['listing_id' => $this->listing->id]);
    }

    public function tags(): array
    {
        return ['notifications', 'listing:' . $this->listing->id, 'type:rejected'];
    }
}
