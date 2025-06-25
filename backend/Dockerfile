FROM php:8.2-fpm

# Instalar dependencias y extensiones PHP necesarias
RUN apt-get update && apt-get install -y \
    zip unzip curl git libpq-dev libonig-dev libxml2-dev libzip-dev default-mysql-client \
    && docker-php-ext-configure zip \
    && docker-php-ext-install pdo pdo_mysql mbstring zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www

# Copiar archivos del proyecto al contenedor
COPY . .

# Instalar dependencias de Laravel optimizadas para producción
RUN composer install --no-dev --optimize-autoloader

# Exponer el puerto 8000 para la app Laravel
EXPOSE 8000

# Comando para arrancar el servidor Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
