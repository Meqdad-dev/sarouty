# 🏠 Sarouty v2 — Plateforme Immobilière Laravel 11

> Version améliorée avec IA, Laravel Horizon, OpenStreetMap, SMS/Email, Paiements

---

## ✨ Nouvelles fonctionnalités vs v1

| Fonctionnalité | v1 | v2 |
|---|---|---|
| Livewire | ✅ | ❌ supprimé → Alpine.js pur + fetch API |
| Laravel Horizon | ❌ | ✅ Queues Redis multi-supervisors |
| Intelligence Artificielle | ❌ | ✅ GPT-4o-mini : descriptions, prix, modération |
| Cartes | Google Maps (iframe) | ✅ OpenStreetMap + Leaflet + Nominatim géocodage |
| SMS | ❌ | ✅ Twilio ou Infobip |
| Emails | ❌ | ✅ Mailgun ou Brevo avec templates HTML |
| Paiements | ❌ | ✅ Stripe – 4 plans (Gratuit/Starter/Pro/Agence) |
| Dashboard Admin | Basic | ✅ Design premium dark, graphiques Chart.js |
| Modération IA | ❌ | ✅ Analyse automatique des annonces suspectes |

---

## 🚀 Installation

### 1. Prérequis
- PHP 8.2+
- MySQL 8.0+ ou MariaDB 10.6+
- Redis (pour Horizon)
- Composer

### 2. Installation des dépendances

```bash
composer install

# Packages v2 (si installation manuelle)
composer require laravel/horizon openai-php/laravel twilio/sdk stripe/stripe-php
```

### 3. Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Remplissez le fichier `.env` avec vos clés API (voir section ci-dessous).

### 4. Base de données

```bash
php artisan migrate
php artisan db:seed
```

### 5. Stockage

```bash
php artisan storage:link
```

### 6. Laravel Horizon

```bash
php artisan horizon:install
php artisan horizon
```

Accès au dashboard : `https://votresite.com/horizon` (admin uniquement)

---

## 🔑 Configuration .env

### SMS : Twilio (recommandé pour le Maroc)
```
TWILIO_SID=ACxxxxxxxxxxxxxxxxxx
TWILIO_TOKEN=xxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_FROM=+212XXXXXXXXX
```

### SMS : Infobip (alternative)
```
INFOBIP_BASE_URL=https://XXXXX.api.infobip.com
INFOBIP_API_KEY=xxxxxxxxxxxx
INFOBIP_FROM=Sarouty
```

### Email : Mailgun
```
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=mg.sarouty.ma
MAILGUN_SECRET=key-xxxxxxxxxxxxxxxx
MAILGUN_ENDPOINT=api.eu.mailgun.net
```

### Email : Brevo (alternative)
```
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=votre@email.com
MAIL_PASSWORD=votre-cle-brevo
MAIL_ENCRYPTION=tls
```

### OpenAI
```
OPENAI_API_KEY=sk-proj-xxxxxxxxxxxxxxxx
```

### Stripe
```
STRIPE_KEY=pk_live_xxxxxxxxxxxxxxxx
STRIPE_SECRET=sk_live_xxxxxxxxxxxxxxxx
```

---

## 🏗 Architecture

```
app/
├── Http/Controllers/
│   ├── Admin/AdminController.php   ← Dashboard + stats + modération
│   └── User/UserListingController.php ← CRUD + IA + favoris AJAX
├── Services/
│   ├── AiService.php        ← OpenAI GPT-4o-mini
│   ├── SmsService.php       ← Twilio / Infobip
│   ├── EmailService.php     ← Mailgun / Brevo
│   ├── GeoService.php       ← OpenStreetMap Nominatim
│   └── PaymentService.php   ← Stripe
├── Jobs/                    ← Traitement asynchrone via Horizon
│   ├── NotifyListingApproved.php  → queue: notifications
│   ├── NotifyListingRejected.php  → queue: notifications
│   └── AiModerateListingJob.php   → queue: ai

resources/views/
├── pages/admin/dashboard.blade.php  ← Nouveau dashboard dark premium
├── pages/user/
│   ├── dashboard.blade.php
│   ├── listings/create.blade.php   ← Formulaire + bouton IA + OSM
│   └── plans.blade.php             ← Plans Stripe
└── emails/
    ├── welcome.blade.php
    ├── listing-approved.blade.php
    ├── listing-rejected.blade.php  (à créer)
    └── contact-message.blade.php   (à créer)
```

---

## 🌍 OpenStreetMap

Le projet utilise **Leaflet.js** avec les tuiles OpenStreetMap (gratuit, sans clé API).

Le géocodage d'adresses utilise **Nominatim** (API gratuite d'OpenStreetMap) :
- Limite : 1 requête/seconde
- Pour la production à fort trafic, hébergez votre propre instance Nominatim ou utilisez l'API Mapbox/LocationIQ

---

## 🤖 Fonctionnalités IA

### 1. Génération de description
Bouton "Générer avec l'IA" dans le formulaire d'annonce. Utilise GPT-4o-mini.

### 2. Analyse de prix
Comparaison automatique du prix demandé avec le marché local.

### 3. Modération automatique
À la soumission de chaque annonce, un job asynchrone analyse le contenu pour détecter :
- Fraudes et arnaques
- Contenus inappropriés
- Prix aberrants

### 4. Suggestions de recherche
API `/api/ai/search-suggestions?q=...` pour des suggestions intelligentes.

---

## 📊 Laravel Horizon

3 queues configurées :

| Queue | Usage | Workers |
|-------|-------|---------|
| `notifications` | SMS + Email | 3 max |
| `ai` | OpenAI (lent) | 2 max |
| `mail` | Newsletters | 2 max |
| `default` | Reste | 5 max |

Dashboard Horizon : `/horizon` (admin seulement)

### Démarrage en production

```bash
# Supervisord config
php artisan horizon
# ou avec supervisord :
php artisan horizon:supervisor
```

---

## 💳 Plans tarifaires

| Plan | Prix | Annonces/mois | Avantages |
|------|------|---------------|-----------|
| Gratuit | 0 MAD | 2 | Photos limitées |
| Starter | 99 MAD | 10 | Mise en avant 3j, Badge |
| Pro | 299 MAD | 50 | Stats avancées, Support prioritaire |
| Agence | 799 MAD | Illimitées | Page agence, API |

---

## 📱 Notifications SMS envoyées

- Inscription → message de bienvenue
- Annonce approuvée → confirmation
- Annonce refusée → motif
- Nouveau message reçu → alerte

---

## 🔄 Suppression de Livewire

Les composants Livewire ont été remplacés par :

| Ancien (Livewire) | Nouveau (Alpine.js + AJAX) |
|---|---|
| `SearchListings` | `fetch('/api/listings/search')` |
| `ToggleFavorite` | `toggleFavorite()` en JS global |

Plus léger, plus rapide, aucune dépendance supplémentaire.
