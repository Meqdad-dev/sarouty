<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Annonce approuvée – Sarouty</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { background:#F5EFE0; font-family:'Outfit',system-ui,sans-serif; color:#1A1410; }
  .wrapper { max-width:580px; margin:0 auto; padding:32px 16px; }
  .card { background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,0.08); }
  .header { background:linear-gradient(135deg,#1A1410 0%,#3D6B20 100%); padding:40px 32px; text-align:center; }
  .logo { display:inline-flex; align-items:center; gap:10px; margin-bottom:24px; }
  .logo-icon { width:44px; height:44px; background:#C8963E; border-radius:12px; display:flex; align-items:center; justify-content:center; }
  .logo-text { font-size:26px; font-weight:700; color:#fff; letter-spacing:-0.5px; }
  .logo-text span { color:#C8963E; }
  .status-badge { display:inline-flex; align-items:center; gap:8px; background:rgba(52,211,153,0.15); border:1px solid rgba(52,211,153,0.3); color:#34D399; padding:8px 20px; border-radius:100px; font-size:13px; font-weight:600; margin-bottom:20px; }
  .header h1 { font-size:28px; font-weight:700; color:#fff; line-height:1.3; }
  .body { padding:36px 32px; }
  .listing-card { background:#F5EFE0; border-radius:12px; padding:20px; margin:24px 0; border-left:3px solid #C8963E; }
  .listing-title { font-size:18px; font-weight:700; color:#1A1410; margin-bottom:6px; }
  .listing-meta { font-size:13px; color:#6B5B45; display:flex; gap:16px; flex-wrap:wrap; }
  .listing-price { font-size:20px; font-weight:700; color:#C8963E; margin-top:10px; }
  .info-text { font-size:15px; line-height:1.7; color:#4A3728; margin:16px 0; }
  .cta-btn { display:inline-block; background:linear-gradient(135deg,#C8963E,#9B6E22); color:#fff; text-decoration:none; padding:14px 32px; border-radius:10px; font-weight:600; font-size:15px; margin:20px 0; }
  .tips { background:#F5EFE0; border-radius:12px; padding:20px 24px; margin:24px 0; }
  .tips h3 { font-size:14px; font-weight:700; color:#1A1410; margin-bottom:12px; text-transform:uppercase; letter-spacing:0.05em; }
  .tip-item { display:flex; align-items:flex-start; gap:10px; margin-bottom:10px; font-size:13px; color:#4A3728; line-height:1.5; }
  .tip-icon { flex-shrink:0; font-size:16px; }
  .footer { background:#1A1410; padding:24px 32px; text-align:center; }
  .footer p { color:rgba(255,255,255,0.4); font-size:12px; line-height:1.8; }
  .footer a { color:#C8963E; text-decoration:none; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="card">

    <!-- Header -->
    <div class="header">
      <div class="logo">
        <img src="{{ asset('logo/logo.png') }}" alt="Sarouty" style="height: 60px; width: auto;">
      </div>
      <div class="status-badge">
        <span>INFO :</span> Annonce approuvée
      </div>
      <h1>Votre annonce est maintenant en ligne !</h1>
    </div>

    <!-- Body -->
    <div class="body">
      <p class="info-text">Bonjour <strong>{{ $listing->user->name }}</strong>,</p>
      <p class="info-text">
        Bonne nouvelle ! Notre équipe a examiné et approuvé votre annonce. Elle est désormais visible par tous les visiteurs de Sarouty.
      </p>

      <!-- Listing card -->
      <div class="listing-card">
        <div class="listing-title">{{ $listing->title }}</div>
        <div class="listing-meta">
          <span>VILLE : {{ $listing->city }}{{ $listing->zone ? ', ' . $listing->zone : '' }}</span> · 
          <span>TYPE : {{ $listing->property_label }}</span>
          @if($listing->surface) · <span>SURFACE : {{ $listing->surface }} m²</span> @endif
        </div>
        <div class="listing-price">{{ $listing->formatted_price }}</div>
      </div>

      <p class="info-text">
        Pour maximiser vos chances de vendre ou louer rapidement, voici quelques conseils :
      </p>

      <div class="tips">
        <h3>Conseils pour booster votre annonce</h3>
        <div class="tip-item">
          <span>• Ajoutez des photos de haute qualité – les annonces avec 8+ photos reçoivent 3x plus de contacts.</span>
        </div>
        <div class="tip-item">
          <span>• Vérifiez que votre numéro de téléphone est à jour dans votre profil pour recevoir les appels des acheteurs.</span>
        </div>
        <div class="tip-item">
          <span>• Pensez à passer à un plan Pro pour mettre votre annonce en avant et gagner encore plus de visibilité.</span>
        </div>
      </div>

      <div style="text-align:center">
        <a href="{{ route('listings.show', $listing) }}" class="cta-btn">
          Voir mon annonce en ligne →
        </a>
      </div>

      <p class="info-text" style="font-size:13px; color:#8a7060; margin-top:16px;">
        Votre annonce sera visible pendant 60 jours. Passé ce délai, reconnectez-vous pour la renouveler si nécessaire.
      </p>
    </div>

    <!-- Footer -->
    <div class="footer">
      <p>
        Vous recevez cet email car vous avez publié une annonce sur <a href="{{ config('app.url') }}">Sarouty.ma</a>.<br>
        © {{ date('Y') }} Sarouty – Votre portail immobilier au Maroc.<br>
        <a href="{{ config('app.url') }}/mon-compte/profil">Gérer mes préférences email</a>
      </p>
    </div>

  </div>
</div>
</body>
</html>
