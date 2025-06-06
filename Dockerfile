FROM php:8.1-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/public

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    zip \
    curl \
    && docker-php-ext-install pdo pdo_mysql zip exif pcntl bcmath gd \
    && pecl install redis \
    && docker-php-ext-enable redis

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN a2enmod rewrite headers

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN mkdir -p /var/www && chown -R www-data:www-data /var/www

WORKDIR /var/www

COPY . /var/www

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# ↓ Te dwie linie dodajemy, aby nadać poprawne prawa:
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

EXPOSE 80
CMD ["apache2-foreground"]
