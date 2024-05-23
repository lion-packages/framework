FROM php:8.3-apache

# Add User
RUN useradd -m lion && echo 'lion:lion' | chpasswd && usermod -aG sudo lion && usermod -s /bin/bash lion

# Dependencies
RUN apt-get update -y \
    && apt-get install -y sudo nano git npm default-mysql-client curl wget unzip cron sendmail libpng-dev libzip-dev \
    && apt-get install -y zlib1g-dev libonig-dev supervisor libevent-dev libssl-dev \
    && rm -rf /var/lib/apt/lists/*

# Electron-Vite Dependencies
RUN apt-get update -y \
    && apt-get install -y libnss3 mesa-utils libgl1-mesa-glx mesa-utils-extra libx11-xcb1 libxcb-dri3-0 libxtst6 \
    && apt-get install -y libasound2 libgtk-3-0 libcups2 libatk-bridge2.0 libatk1.0 libcanberra-gtk-module \
    && apt-get install -y libcanberra-gtk3-module dbus libdbus-1-3 dbus-user-session \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configure PHP-Extensions
RUN pecl install ev redis xdebug \
    && docker-php-ext-install mbstring gd pdo_mysql mysqli zip \
    && docker-php-ext-enable gd zip redis xdebug \
    && a2enmod rewrite

# Configure Xdebug
RUN echo "xdebug.mode=develop,coverage,debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_connect_back=off" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.idekey=docker" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.log=/dev/stdout" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.log_level=0" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9000" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy Data
COPY . .
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Init Project
CMD touch storage/logs/server.log storage/logs/socket.log storage/logs/supervisord.log storage/logs/test-coverage.log \
    && /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
