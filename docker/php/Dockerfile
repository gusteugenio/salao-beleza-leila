FROM php:8.2-fpm

# Dependências do sistema
RUN apt-get update && apt-get install -y \
  git unzip libzip-dev && docker-php-ext-install pdo_mysql zip

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Diretório de trabalho
WORKDIR /var/www

# Expondo porta do PHP-FPM
EXPOSE 9000

# Comando padrão
CMD ["php-fpm"]
