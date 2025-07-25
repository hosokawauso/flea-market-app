FROM php:7.4.9-fpm

# 古いリポジトリからアーカイブに変更
RUN sed -i 's|deb.debian.org|archive.debian.org|g' /etc/apt/sources.list \
  && sed -i 's|security.debian.org|archive.debian.org|g' /etc/apt/sources.list \
  && apt update

COPY php.ini /usr/local/etc/php/

RUN apt install -y \
  default-mysql-client \
  zlib1g-dev \
  libzip-dev \
  unzip \
  libpng-dev \
  libjpeg62-turbo-dev \
  libfreetype6-dev \
  && docker-php-ext-install pdo_mysql zip \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) gd \
  && apt clean \
  && rm -rf /var/lib/apt/lists/*
  
RUN curl -sS https://getcomposer.org/installer | php \
  && mv composer.phar /usr/local/bin/composer \
  && composer self-update

WORKDIR /var/www
