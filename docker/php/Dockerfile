FROM php:8.2-apache

# システムパッケージ
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer インストール
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Apache mod_rewrite 有効化（Laravel 用）
RUN a2enmod rewrite

# ドキュメントルートを public/ に変更
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# AllowOverride All（.htaccess を有効にする）
RUN sed -ri -e 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

WORKDIR /var/www/html
