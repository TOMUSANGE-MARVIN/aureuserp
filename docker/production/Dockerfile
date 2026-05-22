FROM ubuntu:24.04

ARG PHP_VERSION=8.4
ARG NODE_VERSION=22

ARG ADMIN_NAME=Administrator
ARG ADMIN_EMAIL=admin@example.com
ARG ADMIN_PASSWORD=password

ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC

RUN set -eux; \
    printf 'Acquire::ForceIPv4 "true";\nAcquire::Retries "10";\nAcquire::http::Timeout "30";\n' > /etc/apt/apt.conf.d/99aureus-network; \
    for f in /etc/apt/sources.list /etc/apt/sources.list.d/*.list /etc/apt/sources.list.d/*.sources; do \
        [ -f "$f" ] || continue; \
        sed -i 's|https://archive.ubuntu.com/ubuntu|http://us.archive.ubuntu.com/ubuntu|g' "$f"; \
        sed -i 's|http://archive.ubuntu.com/ubuntu|http://us.archive.ubuntu.com/ubuntu|g' "$f"; \
        sed -i 's|https://security.ubuntu.com/ubuntu|http://security.ubuntu.com/ubuntu|g' "$f"; \
    done

RUN apt-get -o Acquire::Retries=5 -o Acquire::http::Timeout=30 -o Acquire::https::Timeout=30 update \
    && printf '#!/bin/sh\nexit 0\n' > /usr/sbin/policy-rc.d \
    && chmod +x /usr/sbin/policy-rc.d \
    && apt-get -o Acquire::Retries=5 -o Acquire::http::Timeout=30 -o Acquire::https::Timeout=30 install -y \
        apt-transport-https \
        ca-certificates \
        curl \
        gnupg \
        software-properties-common \
        unzip \
    && add-apt-repository ppa:ondrej/php -y \
    && apt-get -o Acquire::Retries=5 -o Acquire::http::Timeout=30 -o Acquire::https::Timeout=30 update \
    && apt-get -o Acquire::Retries=5 -o Acquire::http::Timeout=30 -o Acquire::https::Timeout=30 install -y \
        imagemagick \
        libmagickwand-dev \
        mysql-server \
        nginx \
        supervisor \
    && rm -rf /var/lib/apt/lists/*

RUN apt-get -o Acquire::Retries=5 -o Acquire::http::Timeout=30 -o Acquire::https::Timeout=30 update \
    && apt-get -o Acquire::Retries=5 -o Acquire::http::Timeout=30 -o Acquire::https::Timeout=30 install -y \
        php${PHP_VERSION}-bcmath \
        php${PHP_VERSION}-cli \
        php${PHP_VERSION}-curl \
        php${PHP_VERSION}-exif \
        php${PHP_VERSION}-fpm \
        php${PHP_VERSION}-gd \
        php${PHP_VERSION}-gmp \
        php${PHP_VERSION}-imagick \
        php${PHP_VERSION}-intl \
        php${PHP_VERSION}-mbstring \
        php${PHP_VERSION}-mysql \
        php${PHP_VERSION}-soap \
        php${PHP_VERSION}-xml \
        php${PHP_VERSION}-zip \
    && rm -rf /var/lib/apt/lists/*

RUN curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

RUN apt-get autoremove -y \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/aureuserp

# Copy application source (built by Coolify from the repository)
COPY . .

RUN composer install \
        --no-dev \
        --no-interaction \
        --prefer-dist \
        --optimize-autoloader \
        --no-scripts \
    && rm -rf /root/.composer/cache

RUN cp .env.example .env \
    && sed -i 's/^APP_NAME=.*/APP_NAME=AureusERP/' .env \
    && sed -i 's/^APP_DEBUG=.*/APP_DEBUG=false/' .env \
    && sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=mysql/' .env \
    && sed -i 's/^CACHE_STORE=.*/CACHE_STORE=file/' .env \
    && sed -i 's|^SESSION_DRIVER=.*|SESSION_DRIVER=file|' .env \
    && sed -i 's/^# *DB_HOST=.*/DB_HOST=127.0.0.1/' .env \
    && sed -i 's/^# *DB_PORT=.*/DB_PORT=3306/' .env \
    && sed -i 's/^# *DB_DATABASE=.*/DB_DATABASE=aureuserp/' .env \
    && sed -i 's/^# *DB_USERNAME=.*/DB_USERNAME=aureus/' .env \
    && sed -i 's/^# *DB_PASSWORD=.*/DB_PASSWORD=aureus123/' .env \
    && sed -i 's|^APP_URL=.*|APP_URL=http://localhost|' .env \
    && sed -i 's|^FILESYSTEM_DISK=.*|FILESYSTEM_DISK=public|' .env

RUN npm ci --no-audit --no-fund \
    && npm run build \
    && rm -rf node_modules /root/.npm

COPY docker/production/mysql-init.sql /docker-entrypoint-initdb.d/init.sql

RUN sed -i 's/^APP_ENV=.*/APP_ENV=production/' .env

COPY docker/production/php.ini /etc/php/${PHP_VERSION}/fpm/conf.d/99-aureus.ini
COPY docker/production/php.ini /etc/php/${PHP_VERSION}/cli/conf.d/99-aureus.ini
COPY docker/production/php-fpm.conf /etc/php/${PHP_VERSION}/fpm/pool.d/www.conf

RUN rm -f /etc/nginx/sites-enabled/default /etc/nginx/conf.d/default.conf
COPY docker/production/nginx.conf /etc/nginx/conf.d/aureus.conf

COPY docker/production/supervisord.conf /etc/supervisor/conf.d/aureus.conf

RUN mkdir -p /run/php /var/log/supervisor /var/log/aureus

RUN chown -R www-data:www-data /var/www/aureuserp /var/log/aureus \
    && chmod -R 775 storage bootstrap/cache \
    && find storage bootstrap/cache -type d -exec chmod g+s {} +

COPY docker/production/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY docker/production/runtime-install.sh /usr/local/bin/runtime-install.sh
RUN chmod +x /usr/local/bin/entrypoint.sh /usr/local/bin/runtime-install.sh

RUN apt-get purge -y nodejs \
    && apt-get autoremove -y \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=5s --start-period=90s --retries=5 \
    CMD curl -fsS http://127.0.0.1/health || exit 1

ENTRYPOINT ["bash", "/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]
