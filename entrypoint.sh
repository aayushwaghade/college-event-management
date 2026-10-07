#!/bin/sh
set -e

# Default port to 80 if PORT is not set by the hosting platform
PORT="${PORT:-80}"

echo "Starting Apache on PORT: $PORT"

# Update Apache port configuration dynamically
sed -i "s/Listen .*/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost *:$PORT>/g" /etc/apache2/sites-available/000-default.conf

# Execute Apache in foreground
exec apache2-foreground
