# ---------- Base ----------
FROM php:8.2-cli

# ---------- System deps ----------
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    nodejs \
    npm

# ---------- Composer ----------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# ---------- Copy files ----------
COPY . .

# ---------- Install PHP deps ----------
RUN composer install --no-dev --optimize-autoloader

# ---------- Install JS deps + build ----------
RUN npm install
RUN npm run build

# ---------- Permissions ----------
RUN chmod -R 775 storage bootstrap/cache

# ---------- Clear cache ----------
RUN php artisan config:clear
RUN php artisan view:clear
RUN php artisan cache:clear

# ---------- Expose port ----------
EXPOSE 8080

# ---------- Start ----------
CMD php -S 0.0.0.0:${PORT:-8080} -t public



