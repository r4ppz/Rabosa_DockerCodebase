# Custom PHP-FPM image built on top of the Devilbox PHP-FPM 8.2 image
FROM devilbox/php-fpm:8.2-work

# Make sure the runtime tools we rely on are available
RUN sudo apt-get update \
    && sudo apt-get install -y --no-install-recommends \
        git \
        curl \
        unzip \
        zip \
    && sudo rm -rf /var/lib/apt/lists/*

# Latest Composer (works variant already bundles one, this guarantees a current version)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# The web root should be writable by the php-fpm user
RUN sudo chown -R devilbox:devilbox /var/www/html

WORKDIR /var/www/html

EXPOSE 9000

CMD ["php-fpm"]
