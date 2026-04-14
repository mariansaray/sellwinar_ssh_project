#!/bin/bash
# =====================================================
# Sellwinar — Deploy script
# =====================================================

set -e
cd "$(dirname "$0")"

# Nájsť PHP 8.5 alebo 8.3 (server má 8.1 default, ale 8.5 je dostupné)
if [ -x /usr/bin/php85 ]; then
    PHP=/usr/bin/php85
elif [ -x /usr/bin/php83 ]; then
    PHP=/usr/bin/php83
else
    PHP=php
fi

echo ""
echo "=========================================="
echo "  SELLWINAR — Deploy"
echo "  PHP: $($PHP -v 2>&1 | head -1)"
echo "=========================================="
echo ""

# 1. Závislosti — stiahnuť composer ak treba
echo "[1/7] Inštalujem závislosti..."
if [ -f composer.phar ]; then
    $PHP composer.phar install --optimize-autoloader --no-dev
elif command -v composer &> /dev/null; then
    $PHP $(which composer) install --optimize-autoloader --no-dev
else
    echo "    Sťahujem Composer..."
    $PHP -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    $PHP composer-setup.php --quiet
    rm -f composer-setup.php
    $PHP composer.phar install --optimize-autoloader --no-dev
fi

# 1.5 Vytvoriť .env ak neexistuje
if [ ! -f .env ]; then
    echo "    Vytváram .env z premenných prostredia..."
    cat > .env << 'ENVBLOCK'
APP_NAME=Sellwinar
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://sellwinar.com
APP_LOCALE=sk
APP_FALLBACK_LOCALE=sk
BCRYPT_ROUNDS=12
LOG_CHANNEL=stack
LOG_LEVEL=error
DB_CONNECTION=mysql
DB_HOST=vps972.hostcreators.eu
DB_PORT=3306
DB_DATABASE=d68049_sellwinar
DB_USERNAME=u68049_project
DB_PASSWORD=QsS_6-h-s610QBf-
SESSION_DRIVER=database
SESSION_LIFETIME=120
QUEUE_CONNECTION=database
CACHE_STORE=database
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@sellwinar.com
MAIL_FROM_NAME=Sellwinar
ENVBLOCK
fi

# 2. Kľúč
if [ -f .env ] && ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    echo "[2/7] Generujem APP_KEY..."
    $PHP artisan key:generate --force
else
    echo "[2/7] APP_KEY OK."
fi

# 3. Migrácie
echo "[3/7] Spúšťam migrácie..."
$PHP artisan migrate --force

# 4. Seedery
echo "[4/7] Seedery..."
$PHP artisan db:seed --force 2>/dev/null || echo "    Seedery hotové."

# 5. Cache
echo "[5/7] Optimalizujem..."
$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache
$PHP artisan storage:link 2>/dev/null || true

# 6. Permissions
echo "[6/7] Permissions..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# 7. Queue
echo "[7/7] Queue restart..."
$PHP artisan queue:restart 2>/dev/null || true

echo ""
echo "=========================================="
echo "  HOTOVO!"
echo "  Login: admin@sellwinar.com / admin123"
echo "=========================================="
echo ""
