<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestBrevoEmail extends Command
{
    protected $signature   = 'brevo:test {email : Adresse email de destination}';
    protected $description = 'Teste l\'envoi d\'un email via l\'API Brevo';

    public function handle(): int
    {
        $email      = $this->argument('email');
        $apiKey     = config('services.brevo.api_key');
        $senderMail = config('services.brevo.sender_email');
        $senderName = config('services.brevo.sender_name');

        $this->info("=== Test Brevo Email ===");
        $this->line("Clé API   : " . ($apiKey ? substr($apiKey, 0, 15) . '...' : 'NON CONFIGURÉE'));
        $this->line("Expéditeur: {$senderName} <{$senderMail}>");
        $this->line("Destinataire: {$email}");
        $this->newLine();

        if (!$apiKey || str_contains($apiKey, 'your_brevo')) {
            $this->error("❌ BREVO_API_KEY n'est pas configurée dans .env !");
            $this->line("Ajoutez votre clé sur https://app.brevo.com/settings/keys/api");
            return self::FAILURE;
        }

        $this->info("Envoi en cours…");

        $registerUrl = url('/register');

        $html = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<body style="font-family:Arial,sans-serif;background:#f6f7fb;padding:40px 20px;margin:0;">
  <table width="100%" cellpadding="0" cellspacing="0">
    <tr><td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#fff;border-radius:16px;overflow:hidden;">
        <tr>
          <td style="background:linear-gradient(135deg,#1A1410,#2D1F12);padding:36px 40px;text-align:center;">
            <h1 style="margin:0;color:#C8963E;font-size:28px;">Sarouty</h1>
            <p style="margin:6px 0 0;color:rgba(255,255,255,0.6);font-size:13px;">Immobilier au Maroc</p>
          </td>
        </tr>
        <tr>
          <td style="padding:40px;">
            <p style="font-size:16px;color:#111827;">Bonjour,</p>
            <p style="font-size:15px;color:#374151;line-height:1.7;">
              Nous avons bien reçu votre demande d'<strong>estimation immobilière</strong> sur la plateforme <strong>Sarouty</strong>.
            </p>
            <p style="font-size:15px;color:#374151;line-height:1.7;">
              Nous vous proposons de <strong>publier votre annonce gratuitement</strong> sur notre plateforme Sarouty.
            </p>
            <table cellpadding="0" cellspacing="0" style="margin:28px 0 0;">
              <tr>
                <td style="background:#C8963E;border-radius:12px;">
                  <a href="{$registerUrl}" style="display:inline-block;padding:14px 32px;color:#fff;font-weight:700;font-size:16px;text-decoration:none;">
                    Publier maintenant →
                  </a>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td style="background:#f8fafc;border-top:1px solid #e5e7eb;padding:24px 40px;text-align:center;">
            <p style="margin:0;font-size:12px;color:#9ca3af;">
              © Sarouty – Test d'envoi via Brevo API
            </p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;

        try {
            $response = Http::withHeaders([
                'api-key'      => $apiKey,
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ])->post('https://api.brevo.com/v3/smtp/email', [
                'sender'      => ['name' => $senderName, 'email' => $senderMail],
                'to'          => [['email' => $email, 'name' => 'Test Sarouty']],
                'subject'     => '[TEST] Email Sarouty via Brevo',
                'htmlContent' => $html,
                'tags'        => ['test'],
            ]);

            if ($response->successful()) {
                $this->newLine();
                $this->info("✅ Email envoyé avec succès !");
                $this->line("   MessageId: " . ($response->json('messageId') ?? 'N/A'));
                $this->line("   Vérifiez la boîte de réception de {$email}");
                return self::SUCCESS;
            }

            $this->newLine();
            $this->error("❌ Échec : HTTP " . $response->status());
            $this->line("   Réponse : " . $response->body());
            return self::FAILURE;

        } catch (\Throwable $e) {
            $this->error("❌ Exception : " . $e->getMessage());
            return self::FAILURE;
        }
    }
}
