FROM php:8.2-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
    libtirpc-dev \
    libnsl-dev \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libfreetype6-dev \
    libzip-dev \
    libxml2-dev \
    sqlite3 \
    libsqlite3-dev \
    unzip \
    git \
    curl \
    libmongoc-1.0-0 \
    libmongoc-dev \
    libssl-dev \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install gd zip pdo xml \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest

# Install Composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory
WORKDIR /var/www/html

# Copy the composer.json and composer.lock files to the container
COPY composer.json composer.lock ./

# Install PHP dependencies via Composer
RUN composer install --no-scripts --no-autoloader

# Copy the package.json and package-lock.lock files to the container
COPY package.json package-lock.json ./

# Install Node dependencies via Npm
RUN npm install

# Copy the rest of the application code to the container
COPY . .

ADD docker/php.ini /usr/local/etc/php/conf.d/custom.ini

RUN npm run build

RUN rm -rf /var/www/html/node_modules

# Optimize Composer autoloader
RUN composer dump-autoload --optimize

RUN mkdir -p /var/www/html/storage/logs
RUN chown -R www-data:www-data /var/www/html/storage

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    # && chmod -R 775 /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/public/build \
    && chmod -R 775 /var/www/html/database

# Expose port 9000
EXPOSE 9000

# Start PHP-FPM server
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh
ENTRYPOINT ["/start.sh"]