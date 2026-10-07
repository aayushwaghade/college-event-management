# ============================================================
# Dockerfile
# Production Container Image for Vercel Container Deployment
# College Event Management System (PHP 8.2 + Apache)
# ============================================================

FROM php:8.2-apache

# Install required system packages and CA certificates for secure cloud DB SSL connections
RUN apt-get update && apt-get install -y --no-install-recommends \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# Install and enable PHP extensions required by the application
RUN docker-php-ext-install mysqli pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Configure production php.ini and set session save path to /tmp (writable in serverless containers)
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" && \
    echo "session.save_path = /tmp" >> "$PHP_INI_DIR/php.ini" && \
    echo "session.cookie_httponly = 1" >> "$PHP_INI_DIR/php.ini" && \
    echo "session.cookie_samesite = Lax" >> "$PHP_INI_DIR/php.ini" && \
    echo "upload_max_filesize = 10M" >> "$PHP_INI_DIR/php.ini" && \
    echo "post_max_size = 12M" >> "$PHP_INI_DIR/php.ini"

# Set working directory
WORKDIR /var/www/html

# Copy application files into the container
COPY . /var/www/html/

# Copy and make entrypoint script executable
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set correct file ownership for Apache user
RUN chown -R www-data:www-data /var/www/html

# Expose default port (Vercel provides dynamic $PORT at runtime)
ENV PORT=80
EXPOSE 80

# Run entrypoint script
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
