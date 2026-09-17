FROM php:8.2-apache

# Install PostgreSQL extensions for PDO
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Copy project files into Apache public directory
COPY . /var/www/html/

# Expose port 8380 or standard 80
EXPOSE 80