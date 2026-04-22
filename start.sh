#!/bin/sh
set -e

PORT=${PORT:-10000}
echo "=== Sarouty Starting on port $PORT ==="

# ── 1. Générer la config Nginx avec le bon PORT ────────────────────────────
envsubst '${PORT}' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf
echo "✓ Nginx config generated (port: $PORT)"

# ── 2. Laravel: caches de configuration ───────────────────────────────────
echo "=== Laravel bootstrap ==="

# Clear old build-time caches (si présents)
php artisan config:clear 2>/dev/null || true
php artisan cache:clear  2>/dev/null || true

# Reconstruire les caches avec les vraies variables d'env
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✓ Caches built"

# ── 3. Storage link ────────────────────────────────────────────────────────
php artisan storage:link --quiet 2>/dev/null || true
echo "✓ Storage linked"

# ── 4. Migrations ─────────────────────────────────────────────────────────
echo "=== Running migrations ==="
php artisan migrate --force
echo "✓ Migrations done"

# ── 5. Optimisation des permissions ───────────────────────────────────────
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true

# ── 6. Démarrer Nginx + PHP-FPM via Supervisor ────────────────────────────
echo "=== Starting Supervisor (Nginx + PHP-FPM) ==="
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
