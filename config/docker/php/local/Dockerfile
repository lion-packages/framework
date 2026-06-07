FROM php:8.5-fpm

ARG DEBIAN_FRONTEND=noninteractive
# ----------------------------------------------------------------------------------------------------------------------
USER root

# ----------------------------------------------------------------------------------------------------------------------
# Create user
# ----------------------------------------------------------------------------------------------------------------------
RUN useradd -m lion && echo 'lion:lion' | chpasswd && usermod -aG sudo lion && usermod -s /bin/bash lion
# ----------------------------------------------------------------------------------------------------------------------
# Dependencies
# ----------------------------------------------------------------------------------------------------------------------
RUN apt update -y \
    && apt-get install -y --no-install-recommends \
      sudo nano zsh git curl wget unzip cron golang-go \
      libonig-dev libpng-dev libzip-dev libjpeg-dev libfreetype6-dev pkg-config libssl-dev \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && apt-get clean && rm -rf /var/lib/apt/lists/*
# ----------------------------------------------------------------------------------------------------------------------
# PHP Extensions
# ----------------------------------------------------------------------------------------------------------------------
RUN pecl install redis xdebug \
    && docker-php-ext-install mbstring gd zip pdo pdo_mysql \
    && docker-php-ext-enable xdebug redis gd zip

RUN { \
    echo "xdebug.mode=develop,coverage,debug"; \
    echo "xdebug.remote_autostart=off"; \
    echo "xdebug.remote_connect_back=off"; \
    echo "xdebug.start_with_request=yes"; \
    echo "xdebug.idekey=docker"; \
    echo "xdebug.log=/dev/stdout"; \
    echo "xdebug.log_level=0"; \
    echo "xdebug.client_host=host.docker.internal"; \
    echo "xdebug.client_port=9000"; \
} >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN { \
    echo "pm = dynamic"; \
    echo "pm.max_children = 5"; \
    echo "pm.start_servers = 2"; \
    echo "pm.min_spare_servers = 2"; \
    echo "pm.max_spare_servers = 4"; \
    echo "pm.max_requests = 200"; \
    echo "request_terminate_timeout = 30s"; \
} >> /usr/local/etc/php-fpm.d/www.conf

RUN { \
    echo "memory_limit = 512M"; \
    echo "max_execution_time = 30"; \
    echo "expose_php = Off"; \
    echo "display_errors = Off"; \
    echo "log_errors = On"; \
    echo "error_log = /dev/stderr"; \
} > /usr/local/etc/php/conf.d/production.ini
# ----------------------------------------------------------------------------------------------------------------------
USER lion

SHELL ["/bin/bash", "--login", "-i", "-c"]
# ----------------------------------------------------------------------------------------------------------------------
# Install nvm, Node.js and npm
# ----------------------------------------------------------------------------------------------------------------------
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash \
    && source /home/lion/.bashrc \
    && nvm install 24 \
    && npm install -g npm
# ----------------------------------------------------------------------------------------------------------------------
# Install OhMyZsh
# ----------------------------------------------------------------------------------------------------------------------
RUN sh -c "$(wget https://raw.githubusercontent.com/ohmyzsh/ohmyzsh/master/tools/install.sh -O -)"
# ----------------------------------------------------------------------------------------------------------------------
USER root

SHELL ["/bin/bash", "--login", "-c"]
# ----------------------------------------------------------------------------------------------------------------------
# Install logo-ls
# ----------------------------------------------------------------------------------------------------------------------
RUN ARCH=$(uname -m) && \
    if [ "$ARCH" = "x86_64" ]; then \
        wget https://github.com/Yash-Handa/logo-ls/releases/download/v1.3.7/logo-ls_amd64.deb; \
    elif [ "$ARCH" = "aarch64" ]; then \
        wget https://github.com/Yash-Handa/logo-ls/releases/download/v1.3.7/logo-ls_arm64.deb; \
    else \
        echo "Unsupported architecture: $ARCH" && exit 1; \
    fi && \
    dpkg -i logo-ls_*.deb && \
    rm logo-ls_*.deb && \
    curl https://raw.githubusercontent.com/UTFeight/logo-ls-modernized/master/INSTALL | bash
# ----------------------------------------------------------------------------------------------------------------------
# Add configuration in .zshrc
# ----------------------------------------------------------------------------------------------------------------------
RUN echo 'export NVM_DIR="$HOME/.nvm"' >> /home/lion/.zshrc \
    && echo '[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"' >> /home/lion/.zshrc \
    && echo '[ -s "$NVM_DIR/bash_completion" ] && \. "$NVM_DIR/bash_completion"' >> /home/lion/.zshrc \
    && echo 'alias ls="logo-ls"' >> /home/lion/.zshrc \
    && source /home/lion/.zshrc
# ----------------------------------------------------------------------------------------------------------------------
# Copy files / Permissions
# ----------------------------------------------------------------------------------------------------------------------
COPY . .

COPY config/docker/nginx/local/certs/localhost.pem /usr/local/share/ca-certificates/localhost.crt

RUN update-ca-certificates \
    && sed -i 's/^user = www-data/user = lion/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/^group = www-data/group = lion/' /usr/local/etc/php-fpm.d/www.conf \
    && chown -R lion:lion /var/www/html/public \
    && chmod -R u=rwX,go=rX /var/www/html/public

CMD ["php-fpm"]
