<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau message d'un client potentiel</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #10B981, #059669); padding: 30px; text-align: center;">
            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 600;">
                Dar<span style="color: #f4d03f;">Maroc</span>
            </h1>
            <p style="color: rgba(255,255,255,0.9); margin: 10px 0 0 0; font-size: 14px;">
                Nouveau contact pour votre annonce
            </p>
        </div>

        <!-- Content -->
        <div style="padding: 30px;">
            <p style="color: #333; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                Bonjour <strong style="color: #10B981;">{{ $message->receiver->name ?? 'cher annonceur' }}</strong>,
            </p>

            <p style="color: #666; font-size: 14px; line-height: 1.6; margin: 0 0 20px 0;">
                Un client potentiel vient de vous envoyer un message concernant votre annonce :<br>
                <strong style="color: #10B981;">"{{ $message->listing->title ?? 'Annonce immobilière' }}"</strong>.
            </p>

            <!-- Client Info Box -->
            <div style="background-color: #f8fafc; border-left: 4px solid #10B981; padding: 15px; margin: 20px 0; border-radius: 0 8px 8px 0;">
                <p style="color: #10B981; font-size: 12px; margin: 0 0 10px 0; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">
                    Informations du contact
                </p>
                <div style="color: #333; font-size: 14px; line-height: 1.8; margin: 0;">
                    <strong>Nom :</strong> {{ $message->sender_name }}<br>
                    <strong>Email :</strong> <a href="mailto:{{ $message->sender_email }}" style="color: #10B981;">{{ $message->sender_email }}</a><br>
                    @if($message->sender_phone)
                    <strong>Téléphone :</strong> <a href="tel:{{ $message->sender_phone }}" style="color: #10B981;">{{ $message->sender_phone }}</a><br>
                    @endif
                </div>
            </div>

            <!-- Message Text -->
            <div style="background-color: #f8f9fa; border: 1px solid #eee; padding: 20px; margin: 20px 0; border-radius: 8px;">
                <p style="color: #888; font-size: 12px; margin: 0 0 10px 0; text-transform: uppercase; letter-spacing: 0.5px;">
                    Son message
                </p>
                <p style="color: #555; font-size: 14px; line-height: 1.6; margin: 0; white-space: pre-wrap;">
                    {{ $message->message }}
                </p>
            </div>

            <!-- CTA Button -->
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('user.messages.show', $message->id) }}" 
                   style="display: inline-block; background: linear-gradient(135deg, #10B981, #059669); color: #ffffff; padding: 14px 30px; text-decoration: none; border-radius: 30px; font-weight: 600; font-size: 14px;">
                    Répondre depuis le tableau de bord
                </a>
            </div>

            <p style="color: #888; font-size: 13px; line-height: 1.6; margin: 20px 0 0 0;">
                L'équipe de modération a validé ce message. Vous pouvez recontacter ce client directement ou utiliser votre messagerie interne sur la plateforme.<br><br>
                Cordialement,<br>
                <strong style="color: #10B981;">L'équipe {{ config('app.name', 'Sarouty') }}</strong>
            </p>
        </div>

        <!-- Footer -->
        <div style="background-color: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #eee;">
            <p style="color: #888; font-size: 12px; margin: 0 0 10px 0;">
                © {{ date('Y') }} {{ config('app.name', 'Sarouty') }} – Plateforme immobilière
            </p>
            <p style="color: #aaa; font-size: 11px; margin: 0;">
                Cet email vous a été envoyé automatiquement suite à une demande sur l'une de vos annonces.
            </p>
        </div>
    </div>
</body>
</html>
