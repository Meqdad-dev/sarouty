<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Channels\BrevoEmailChannel;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [BrevoEmailChannel::class];
    }

    /**
     * Get the Brevo representation of the notification.
     */
    public function toBrevo(object $notifiable): array
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $recipientName = $notifiable->name ?: 'Utilisateur';
        $year = now()->year;

        $htmlContent = '<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Réinitialisation de mot de passe - Sarouty</title>
</head>
<body style="margin:0;padding:0;background:#f6f7fb;font-family:Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f6f7fb;padding:40px 20px;">
    <tr><td align="center">
      <table width="600" cellpadding="0" cellspacing="0"
             style="max-width:600px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.06);">

        <tr>
          <td style="background:linear-gradient(135deg,#1A1410,#2D1F12);padding:36px 40px;text-align:center;">
            <h1 style="margin:0;font-size:28px;font-weight:700;color:#C8963E;letter-spacing:-0.5px;">Sarouty</h1>
            <p style="margin:6px 0 0;color:rgba(255,255,255,0.6);font-size:13px;">Immobilier au Maroc</p>
          </td>
        </tr>

        <tr>
          <td style="padding:40px;">
            <p style="margin:0 0 18px;font-size:16px;color:#111827;">Bonjour <strong>' . htmlspecialchars($recipientName) . '</strong>,</p>

            <p style="margin:0 0 18px;font-size:15px;color:#374151;line-height:1.7;">
              Vous recevez cet e-mail car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte <strong>Sarouty</strong>.
            </p>

            <table cellpadding="0" cellspacing="0" style="margin:0 0 28px;">
              <tr>
                <td style="background:#C8963E;border-radius:12px;text-align:center;">
                  <a href="' . $resetUrl . '"
                     style="display:inline-block;padding:14px 32px;font-size:16px;font-weight:700;color:#ffffff;text-decoration:none;">
                    Réinitialiser le mot de passe
                  </a>
                </td>
              </tr>
            </table>

            <p style="margin:0 0 12px;font-size:14px;color:#6b7280;">
              Si vous n\'avez pas demandé la réinitialisation de mot de passe, aucune autre action n\'est requise.
            </p>
          </td>
        </tr>

        <tr>
          <td style="background:#f8fafc;border-top:1px solid #e5e7eb;padding:24px 40px;text-align:center;">
            <p style="margin:0;font-size:12px;color:#9ca3af;">
              &copy; ' . $year . ' Sarouty &ndash; Tous droits réservés<br>
            </p>
          </td>
        </tr>

      </table>
    </td></tr>
  </table>
</body>
</html>';

        return [
            'subject'     => 'Réinitialisation de votre mot de passe – Sarouty',
            'htmlContent' => $htmlContent,
            'tags'        => ['password_reset'],
        ];
    }
}
