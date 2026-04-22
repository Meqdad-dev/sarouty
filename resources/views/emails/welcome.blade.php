<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Bienvenue sur Sarouty</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { background:#F5EFE0; font-family:system-ui,sans-serif; color:#1A1410; }
  .wrapper { max-width:580px; margin:0 auto; padding:32px 16px; }
  .card { background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,0.08); }
  .header { background:linear-gradient(135deg,#1A1410,#3D6B20); padding:48px 32px 40px; text-align:center; }
  .logo-text { font-size:32px; font-weight:700; color:#fff; }
  .logo-text span { color:#C8963E; }
  .tagline { color:rgba(255,255,255,0.5); font-size:13px; margin-top:4px; }
  .hero-emoji { font-size:56px; margin:24px 0 20px; display:block; }
  .header h1 { font-size:26px; font-weight:700; color:#fff; line-height:1.3; }
  .body { padding:36px 32px; }
  .greeting { font-size:17px; color:#1A1410; font-weight:600; margin-bottom:12px; }
  .text { font-size:15px; line-height:1.7; color:#4A3728; margin-bottom:16px; }
  .features { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin:24px 0; }
  .feature { background:#F5EFE0; border-radius:12px; padding:16px; text-align:center; }
  .feature-icon { font-size:24px; margin-bottom:8px; display:block; }
  .feature-title { font-size:13px; font-weight:700; color:#1A1410; }
  .feature-desc { font-size:12px; color:#7a6550; margin-top:4px; line-height:1.4; }
  .cta-btn { display:block; background:linear-gradient(135deg,#C8963E,#9B6E22); color:#fff; text-decoration:none; padding:15px 32px; border-radius:10px; font-weight:700; font-size:15px; text-align:center; margin:24px 0; }
  .footer { background:#1A1410; padding:24px; text-align:center; }
  .footer p { color:rgba(255,255,255,0.4); font-size:12px; line-height:1.8; }
  .footer a { color:#C8963E; text-decoration:none; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="card">
    <div class="header">
      <div class="logo-text">Dar<span>Maroc</span></div>
      <div class="tagline">Votre portail immobilier au Maroc</div>
      <div style="font-size:40px; margin:20px 0; color:#C8963E;">Sarouty</div>
      <h1>Bienvenue sur Sarouty, {{ $user->name }} !</h1>
    </div>
    <div class="body">
      <p class="greeting">Votre compte est prêt</p>
      <p class="text">
        Nous sommes ravis de vous accueillir sur la plateforme immobilière de référence au Maroc. Que vous souhaitiez acheter, vendre, louer ou trouver votre prochain chez-vous, Sarouty est là pour vous accompagner.
      </p>

      <div class="features">
        <div class="feature">
          <span class="feature-icon">🔍</span>
          <div class="feature-title">Recherche avancée</div>
          <div class="feature-desc">Filtrez par ville, type, prix et bien plus</div>
        </div>
        <div class="feature">
          <span class="feature-icon">❤️</span>
          <div class="feature-title">Favoris</div>
          <div class="feature-desc">Sauvegardez les annonces qui vous plaisent</div>
        </div>
        <div class="feature">
          <span class="feature-icon">🤖</span>
          <div class="feature-title">IA intégrée</div>
          <div class="feature-desc">Décrivez votre bien en quelques secondes</div>
        </div>
        <div class="feature">
          <span class="feature-icon">🗺️</span>
          <div class="feature-title">Carte interactive</div>
          <div class="feature-desc">Visualisez les biens sur OpenStreetMap</div>
        </div>
      </div>

      <a href="{{ route('listings.index') }}" class="cta-btn">
        Explorer les annonces →
      </a>

      <p class="text" style="font-size:13px;color:#8a7060;">
        Vous souhaitez publier une annonce ? Connectez-vous à votre espace personnel pour créer votre première annonce gratuitement.
      </p>
    </div>
    <div class="footer">
      <p>
        Sarouty · Votre partenaire immobilier au Maroc<br>
        <a href="{{ config('app.url') }}">sarouty.ma</a> · 
        <a href="{{ config('app.url') }}/mon-compte/profil">Se désabonner</a>
      </p>
    </div>
  </div>
</div>
</body>
</html>
