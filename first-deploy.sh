#!/bin/bash
# =====================================================
# Sellwinar — PRVÝ DEPLOY (jednorázovo)
# Spusti na serveri po SSH prihlásení
# =====================================================

set -e

PROJECT_DIR="/var/www/sellwinar"

echo ""
echo "=========================================="
echo "  SELLWINAR — Prvý deploy"
echo "=========================================="
echo ""

# 1. Klonovanie
if [ -d "$PROJECT_DIR" ]; then
    echo "[1/8] Projekt už existuje, aktualizujem..."
    cd "$PROJECT_DIR"
    if git remote get-url origin 2>/dev/null | grep -q '^https://'; then
        echo "    Nastavujem Git remote na SSH (produkcia)..."
        git remote set-url origin git@github.com:mariansaray/sellwinar_ssh_project.git
    fi
    git pull origin main
else
    echo "[1/8] Klonem projekt z GitHubu (SSH)..."
    # Vyžaduje deploy key alebo SSH kľúč servera pridaný v GitHub → Repository settings → Deploy keys
    git clone git@github.com:mariansaray/sellwinar_ssh_project.git "$PROJECT_DIR"
    cd "$PROJECT_DIR"
fi

# 2. .env súbor
echo "[2/8] Nastavujem .env..."
if [ ! -f .env ]; then
    cp .env.production .env
    echo "    .env vytvorený z .env.production"
fi

# 3. Závislosti
echo "[3/8] Inštalujem PHP závislosti..."
composer install --optimize-autoloader --no-dev --quiet 2>/dev/null || composer install --optimize-autoloader --no-dev

# 4. APP_KEY
echo "[4/8] Generujem APP_KEY..."
php artisan key:generate --force

# 5. Migrácie + seedery
echo "[5/8] Vytváram databázové tabuľky..."
php artisan migrate --force
echo "[5/8] Naplňam dáta..."
php artisan db:seed --force

# 6. Permissions
echo "[6/8] Nastavujem permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# 7. Cache
echo "[7/8] Optimalizujem..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link 2>/dev/null || true

# 8. Queue worker + Scheduler
echo "[8/8] Nastavujem cron a queue..."

# Cron job pre Laravel Scheduler
(crontab -l 2>/dev/null | grep -v "sellwinar"; echo "* * * * * cd $PROJECT_DIR && php artisan schedule:run >> /dev/null 2>&1") | crontab -

# Queue worker na pozadí
php artisan queue:restart 2>/dev/null || true
nohup php artisan queue:work database --sleep=3 --tries=3 --max-time=3600 > /dev/null 2>&1 &

echo ""
echo "=========================================="
echo "  PRVÝ DEPLOY HOTOVÝ!"
echo ""
echo "  Teraz treba nasmerovať webserver"
echo "  (Apache/Nginx) na:"
echo "  $PROJECT_DIR/public"
echo ""
echo "  Prihlásiť sa:"
echo "  https://sellwinar.com/login"
echo "  admin@sellwinar.com / admin123"
echo ""
echo "  !!! PO PRVOM PRIHLÁSENÍ ZMEŇTE HESLO !!!"
echo "=========================================="
echo ""
