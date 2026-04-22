<?php

namespace App\Services;

use App\Models\Listing;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    /**
     * Email de bienvenue après inscription.
     */
    public function sendWelcome(User $user): void
    {
        try {
            Mail::send('emails.welcome', ['user' => $user], function ($mail) use ($user) {
                $mail->to($user->email, $user->name)
                     ->subject('🏠 Bienvenue sur Sarouty – Votre compte est créé !');
            });
        } catch (\Exception $e) {
            Log::error('Email welcome error: ' . $e->getMessage());
        }
    }

    /**
     * Email de confirmation d'annonce soumise.
     */
    public function sendListingSubmitted(Listing $listing): void
    {
        try {
            Mail::send('emails.listing-submitted', ['listing' => $listing], function ($mail) use ($listing) {
                $mail->to($listing->user->email, $listing->user->name)
                     ->subject("⏳ Annonce reçue – {$listing->title}");
            });
        } catch (\Exception $e) {
            Log::error('Email listing submitted error: ' . $e->getMessage());
        }
    }

    /**
     * Email quand une annonce est approuvée.
     */
    public function sendListingApproved(Listing $listing): void
    {
        try {
            Mail::send('emails.listing-approved', ['listing' => $listing], function ($mail) use ($listing) {
                $mail->to($listing->user->email, $listing->user->name)
                     ->subject("✅ Annonce approuvée – {$listing->title}");
            });
        } catch (\Exception $e) {
            Log::error('Email listing approved error: ' . $e->getMessage());
        }
    }

    /**
     * Email quand une annonce est refusée.
     */
    public function sendListingRejected(Listing $listing): void
    {
        try {
            Mail::send('emails.listing-rejected', ['listing' => $listing], function ($mail) use ($listing) {
                $mail->to($listing->user->email, $listing->user->name)
                     ->subject("❌ Annonce non approuvée – {$listing->title}");
            });
        } catch (\Exception $e) {
            Log::error('Email listing rejected error: ' . $e->getMessage());
        }
    }

    /**
     * Email de message de contact reçu (au propriétaire).
     */
    public function sendContactMessage(Message $message): void
    {
        try {
            Mail::send('emails.contact-message', ['message' => $message], function ($mail) use ($message) {
                $mail->to($message->receiver->email, $message->receiver->name)
                     ->replyTo($message->sender_email, $message->sender_name)
                     ->subject("💬 Nouveau message – {$message->listing->title}");
            });
        } catch (\Exception $e) {
            Log::error('Email contact error: ' . $e->getMessage());
        }
    }

    /**
     * Email de réinitialisation du mot de passe.
     */
    public function sendPasswordReset(User $user, string $url): void
    {
        try {
            Mail::send('emails.password-reset', ['user' => $user, 'url' => $url], function ($mail) use ($user) {
                $mail->to($user->email, $user->name)
                     ->subject('🔐 Réinitialisation de votre mot de passe Sarouty');
            });
        } catch (\Exception $e) {
            Log::error('Email password reset error: ' . $e->getMessage());
        }
    }

    /**
     * Email hebdomadaire d'alertes de nouvelles annonces.
     */
    public function sendListingAlert(User $user, array $listings): void
    {
        try {
            Mail::send('emails.listing-alert', ['user' => $user, 'listings' => $listings], function ($mail) use ($user) {
                $mail->to($user->email, $user->name)
                     ->subject('🏠 Nouvelles annonces correspondant à vos critères – Sarouty');
            });
        } catch (\Exception $e) {
            Log::error('Email listing alert error: ' . $e->getMessage());
        }
    }

    /**
     * Generic send method for custom emails.
     */
    public function send(string $to, string $subject, string $view, array $data = []): void
    {
        try {
            Mail::send($view, $data, function ($mail) use ($to, $subject) {
                $mail->to($to)
                     ->subject($subject);
            });
        } catch (\Exception $e) {
            Log::error('Email send error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Email de réponse à un message de contact.
     */
    public function sendMessageReply(Message $originalMessage, string $replyContent, string $adminName): void
    {
        try {
            Mail::send('emails.message-reply', [
                'originalMessage' => $originalMessage,
                'replyContent' => $replyContent,
                'adminName' => $adminName,
            ], function ($mail) use ($originalMessage) {
                $mail->to($originalMessage->sender_email, $originalMessage->sender_name)
                     ->subject("Re: Votre message concernant \"{$originalMessage->listing->title}\"");
            });
        } catch (\Exception $e) {
            Log::error('Email message reply error: ' . $e->getMessage());
            throw $e;
        }
    }
}
