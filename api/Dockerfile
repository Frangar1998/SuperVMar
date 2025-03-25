FROM php:8.4-fpm

RUN apt update \
    && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev unzip supervisor cron \
    && docker-php-ext-install zip intl opcache pdo pdo_mysql sockets \
    && pecl install apcu xdebug-3.4.1 \
    && docker-php-ext-enable apcu \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN chmod -R 775 /var/www/ \
    && usermod -o -u 1000 www-data -d /home/www-data \
    && groupmod -o -g 1000 www-data \
    && mkdir -p /home/www-data && chown www-data:www-data /home/www-data \
    && chown -R www-data:www-data /var/www

COPY php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY supervisor/supervisord.conf /etc/supervisord.conf
COPY php/xdebug/xdebug.ini /usr/local/etc/php/conf.d/

RUN mkdir -p /var/log/supervisor

WORKDIR /var/www/html/api-vmar

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN curl -sS https://get.symfony.com/cli/installer | bash
RUN mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

EXPOSE 80