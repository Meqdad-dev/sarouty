<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrevoEmailChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        // Get the data from the notification
        $data = $notification->toBrevo($notifiable);

        $apiKey      = config('services.brevo.api_key');
        $senderEmail = config('services.brevo.sender_email', 'radouane.bennassir@gmail.com');
        $senderName  = config('services.brevo.sender_name', 'Sarouty');

        if (!$apiKey || str_contains($apiKey, 'your_brevo')) {
            Log::error('Brevo API key is missing or invalid.');
            return;
        }

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'api-key'      => $apiKey,
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ])->post('https://api.brevo.com/v3/smtp/email', [
                'sender'      => ['name' => $senderName, 'email' => $senderEmail],
                'to'          => [['email' => $notifiable->email, 'name' => $notifiable->name ?? 'Utilisateur']],
                'replyTo'     => ['email' => $senderEmail, 'name'  => $senderName],
                'subject'     => $data['subject'] ?? 'Notification de Sarouty',
                'htmlContent' => $data['htmlContent'],
                'headers'     => ['X-Mailer' => 'Sarouty Platform'],
                'tags'        => $data['tags'] ?? ['notification'],
            ]);

            if ($response->successful()) {
                Log::info('Brevo email sent via channel', [
                    'to'         => $notifiable->email,
                    'message_id' => $response->json('messageId'),
                ]);
            } else {
                Log::error('Brevo API error in channel', [
                    'response' => $response->body(),
                    'status'   => $response->status()
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Brevo send exception in channel', ['error' => $e->getMessage()]);
        }
    }
}
