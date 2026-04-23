<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réponse à votre message</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #1A1410, #2D1F12); padding: 36px; text-align: center;">
            <a href="{{ url('/') }}" style="text-decoration:none;">
                <h1 style="margin:0;font-size:28px;font-weight:700;color:#C8963E;letter-spacing:-0.5px;">Sarouty</h1>
            </a>
            <p style="margin:6px 0 0;color:rgba(255,255,255,0.6);font-size:13px;">
                Réponse à votre message
            </p>
        </div>

        <!-- Content -->
        <div style="padding: 30px;">
            <p style="color: #333; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                Bonjour {{ $originalMessage->sender_name ?? 'cher utilisateur' }},
            </p>

            <p style="color: #666; font-size: 14px; line-height: 1.6; margin: 0 0 20px 0;">
                Vous avez reçu une réponse concernant votre message sur l'annonce 
                <strong style="color: #C8963E;">"{{ $listingTitle ?? $originalMessage->listing->title ?? 'Annonce' }}"</strong>.
            </p>

            <!-- Original Message -->
            <div style="background-color: #f8f9fa; border-left: 4px solid #ddd; padding: 15px; margin: 20px 0; border-radius: 0 8px 8px 0;">
                <p style="color: #888; font-size: 12px; margin: 0 0 10px 0; text-transform: uppercase; letter-spacing: 0.5px;">
                    Votre message original
                </p>
                <p style="color: #555; font-size: 14px; line-height: 1.6; margin: 0;">
                    {{ Str::limit($originalMessage->message, 200) }}
                </p>
            </div>

            <!-- Reply -->
            <div style="background-color: #fef9e7; border-left: 4px solid #C8963E; padding: 15px; margin: 20px 0; border-radius: 0 8px 8px 0;">
                <p style="color: #C8963E; font-size: 12px; margin: 0 0 10px 0; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">
                    Réponse de {{ $adminName ?? 'Support Sarouty' }}
                </p>
                <p style="color: #333; font-size: 14px; line-height: 1.8; margin: 0; white-space: pre-wrap;">
                    {{ $replyContent }}
                </p>
            </div>

            <!-- CTA Button -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('listings.show', $originalMessage->listing->id ?? 1) }}" 
                   style="display: inline-block; background: #C8963E; color: #ffffff; padding: 14px 30px; text-decoration: none; border-radius: 30px; font-weight: 600; font-size: 14px;">
                    Voir l'annonce
                </a>
            </div>

            <p style="color: #888; font-size: 13px; line-height: 1.6; margin: 20px 0 0 0;">
                Cordialement,<br>
                <strong style="color: #C8963E;">{{ $adminName ?? 'L\'équipe Sarouty' }}</strong>
            </p>
        </div>

        <!-- Footer -->
        <div style="background-color: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #eee;">
            <p style="color: #888; font-size: 12px; margin: 0 0 10px 0;">
                © {{ date('Y') }} Sarouty – Plateforme immobilière marocaine
            </p>
            <p style="color: #aaa; font-size: 11px; margin: 0;">
                <a href="{{ url('/') }}" style="color: #C8963E; text-decoration: none;">Accueil</a>
                &nbsp;•&nbsp;
                <a href="{{ route('listings.index') }}" style="color: #C8963E; text-decoration: none;">Annonces</a>
                &nbsp;•&nbsp;
                <a href="{{ url('/contact') }}" style="color: #C8963E; text-decoration: none;">Contact</a>
            </p>
        </div>
    </div>
</body>
</html>
