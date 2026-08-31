FROM php:8.3-apache

# Define a imagem base do PHP 8.3 com Apache

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# Instala dependências necessárias para Projeto e PostgreSQL

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia o Composer para dentro do container

WORKDIR /var/www/html

# Define a pasta principal da aplicação

COPY . .

# Copia todos os arquivos do projeto para dentro do container

RUN composer install --no-dev --optimize-autoloader

# Instala as dependências do Projeto em modo produção

RUN cp .env.example .env

# Cria automaticamente o arquivo .env

RUN php artisan key:generate

# Gera automaticamente a APP_KEY do Projeto

RUN chown -R www-data:www-data /var/www/html/storage
RUN chown -R www-data:www-data /var/www/html/bootstrap/cache

# Define permissões necessárias para cache, logs e sessões

RUN a2enmod rewrite

# Ativa o módulo rewrite do Apache necessário para as rotas do Projeto

COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# Copia a configuração personalizada do Apache

EXPOSE 80

# Libera a porta 80 para acesso da aplicação