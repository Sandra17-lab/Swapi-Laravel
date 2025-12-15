FROM php:8.2-cli

# Dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    && docker-php-ext-install zip

# Instalar Node.js (para Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copiar proyecto
COPY . .

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader

# Instalar dependencias JS y compilar Vite
RUN npm install
RUN npm run build

EXPOSE 8080

CMD ["/bin/sh", "-c", "php -S 0.0.0.0:$PORT -t public"]

RUN php artisan config:clear
RUN php artisan cache:clear
RUN php artisan view:clear



