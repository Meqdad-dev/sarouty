<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Estimation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EstimationController extends Controller
{
    /**
     * List all estimations with search / filter.
     */
    public function index(Request $request): View
    {
        $query = Estimation::latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('contact_name',  'like', "%{$search}%")
                  ->orWhere('contact_email', 'like', "%{$search}%")
                  ->orWhere('contact_phone', 'like', "%{$search}%")
                  ->orWhere('city',          'like', "%{$search}%");
            });
        }

        if ($type = $request->input('type')) {
            $query->where('property_type', $type);
        }

        if ($txn = $request->input('transaction')) {
            $query->where('transaction_type', $txn);
        }

        if ($city = $request->input('city')) {
            $query->where('city', $city);
        }

        if ($request->input('with_email')) {
            $query->whereNotNull('contact_email');
        }

        $estimations = $query->paginate(20)->withQueryString();

        $stats = [
            'total'      => Estimation::count(),
            'with_email' => Estimation::whereNotNull('contact_email')->count(),
            'today'      => Estimation::whereDate('created_at', today())->count(),
            'avg_price'  => Estimation::whereNotNull('estimated_mid')->avg('estimated_mid'),
        ];

        return view('pages.admin.estimations.index', compact('estimations', 'stats'));
    }

    /**
     * Show a single estimation detail.
     */
    public function show(Estimation $estimation): View
    {
        return view('pages.admin.estimations.show', compact('estimation'));
    }

    /**
     * Delete an estimation.
     */
    public function destroy(Estimation $estimation): RedirectResponse
    {
        $estimation->delete();
        return back()->with('success', 'Estimation supprimée.');
    }

    /**
     * Send a contact/invite email via Brevo API.
     */
    public function sendEmail(Estimation $estimation): JsonResponse
    {
        if (!$estimation->contact_email) {
            return response()->json(['success' => false, 'message' => 'Aucun email fourni pour cette estimation.'], 422);
        }

        $apiKey      = config('services.brevo.api_key');
        $senderEmail = config('services.brevo.sender_email', 'radouane.bennassir@gmail.com');
        $senderName  = config('services.brevo.sender_name', 'Sarouty');

        if (!$apiKey || str_contains($apiKey, 'your_brevo')) {
            return response()->json(['success' => false, 'message' => 'Clé API Brevo non configurée.'], 500);
        }

        $recipientName = $estimation->contact_name ?: 'Cher client';
        $registerUrl   = url('/register');
        $listingsUrl   = url('/annonces');
        $year          = now()->year;

        $htmlContent = '<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Votre estimation - Sarouty</title>
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
              Nous avons bien reçu votre demande d\'<strong>estimation immobilière</strong> dans la platform <strong>Sarouty</strong>.
            </p>

            <p style="margin:0 0 24px;font-size:15px;color:#374151;line-height:1.7;">
              Nous vous proposons de publier votre annonce gratuitement sur notre plateforme Sarouty.
            </p>

            <table cellpadding="0" cellspacing="0" style="margin:0 0 28px;">
              <tr>
                <td style="background:#C8963E;border-radius:12px;text-align:center;">
                  <a href="' . $registerUrl . '"
                     style="display:inline-block;padding:14px 32px;font-size:16px;font-weight:700;color:#ffffff;text-decoration:none;">
                    Publier maintenant
                  </a>
                </td>
              </tr>
            </table>

            <p style="margin:0 0 12px;font-size:14px;color:#6b7280;">
              Vous pouvez aussi parcourir nos annonces disponibles :
            </p>
            <a href="' . $listingsUrl . '" style="color:#C8963E;font-size:14px;">' . $listingsUrl . '</a>
          </td>
        </tr>

        <tr>
          <td style="background:#f8fafc;border-top:1px solid #e5e7eb;padding:24px 40px;text-align:center;">
            <p style="margin:0;font-size:12px;color:#9ca3af;">
              &copy; ' . $year . ' Sarouty &ndash; Tous droits r&eacute;serv&eacute;s<br>
              Vous recevez cet email car vous avez effectu&eacute; une estimation sur sarouty.ma
            </p>
          </td>
        </tr>

      </table>
    </td></tr>
  </table>
</body>
</html>';

        try {
            $response = Http::withHeaders([
                'api-key'      => $apiKey,
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ])->post('https://api.brevo.com/v3/smtp/email', [
                'sender'  => ['name' => $senderName, 'email' => $senderEmail],
                'to'      => [['email' => $estimation->contact_email, 'name' => $recipientName]],
                'replyTo' => ['email' => $senderEmail, 'name'  => $senderName],
                'subject'     => 'Votre estimation immobilière – Sarouty',
                'htmlContent' => $htmlContent,
                'headers'     => ['X-Mailer' => 'Sarouty Platform'],
                'tags'        => ['estimation', 'invite'],
            ]);

            if ($response->successful()) {
                Log::info('Brevo email sent', [
                    'estimation_id' => $estimation->id,
                    'to'            => $estimation->contact_email,
                    'message_id'    => $response->json('messageId'),
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Email envoyé avec succès à ' . $estimation->contact_email,
                ]);
            }

            Log::error('Brevo API error', ['response' => $response->body(), 'status' => $response->status()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur Brevo : ' . ($response->json('message') ?? $response->body()),
            ], 500);

        } catch (\Throwable $e) {
            Log::error('Brevo send exception', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()], 500);
        }
    }

    /**
     * Bulk send emails.
     */
    public function sendBulkEmail(Request $request): JsonResponse
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'Aucune estimation sélectionnée.'], 422);
        }

        $estimations = Estimation::whereIn('id', $ids)->whereNotNull('contact_email')->get();
        $sent = 0;
        $errors = 0;

        foreach ($estimations as $estimation) {
            $result = $this->sendEmail($estimation);
            $data   = json_decode($result->getContent(), true);
            if ($data['success'] ?? false) {
                $sent++;
            } else {
                $errors++;
            }
            usleep(150_000); // 150ms entre chaque envoi
        }

        return response()->json([
            'success' => true,
            'message' => "{$sent} email(s) envoyé(s)" . ($errors > 0 ? ", {$errors} erreur(s)." : '.'),
        ]);
    }
}
