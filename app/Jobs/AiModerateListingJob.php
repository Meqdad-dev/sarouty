<?php

namespace App\Jobs;

use App\Models\Listing;
use App\Services\AiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AiModerateListingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 120;

    public function __construct(public Listing $listing) {}

    public function handle(AiService $ai): void
    {
        $result = $ai->moderateListing($this->listing);

        // Sauvegarde le résultat de modération
        $this->listing->aiModeration()->updateOrCreate(
            ['listing_id' => $this->listing->id],
            [
                'approved'   => $result['approved'] ?? true,
                'risk_score' => $result['risk_score'] ?? 0,
                'flags'      => json_encode($result['flags'] ?? []),
                'reason'     => $result['reason'] ?? null,
                'model'      => 'gpt-4o-mini',
            ]
        );

        // Si risque très élevé (>80), notifie les admins
        if (($result['risk_score'] ?? 0) >= 80) {
            Log::warning("High risk listing detected by AI", [
                'listing_id' => $this->listing->id,
                'risk_score' => $result['risk_score'],
                'flags'      => $result['flags'],
            ]);
        }
    }

    public function tags(): array
    {
        return ['ai', 'moderation', 'listing:' . $this->listing->id];
    }
}
