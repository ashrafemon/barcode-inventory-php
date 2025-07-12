#!/bin/sh

if [ "$APP_ENV" != "production" ]; then
    php /var/www/html/artisan migrate &
fi

# Start the web server in the background
php-fpm &

# Start the queue worker in the background
php /var/www/html/artisan queue:work --tries=3 --timeout=0 &

# Start the scheduler in the background
while true; do
    php /var/www/html/artisan schedule:run >> /var/log/scheduler.log 2>&1
    sleep 60
done