#!/bin/sh
set -e

# Wait for database connection if DB_HOST is set
if [ -n "$DB_HOST" ]; then
    echo "Waiting for database connection at $DB_HOST:$DB_PORT..."
    # A simple loop to wait for MySQL port to open (if mysql is database connection)
    if [ "$DB_CONNECTION" = "mysql" ]; then
        until php -r "
            \$ip = gethostbyname('$DB_HOST');
            \$port = (int)'${DB_PORT:-3306}';
            \$connection = @fsockopen(\$ip, \$port, \$errno, \$errstr, 2);
            if (is_resource(\$connection)) {
                fclose(\$connection);
                exit(0);
            }
            exit(1);
        "; do
            echo "Database is unavailable - sleeping..."
            sleep 2
        done
        echo "Database is up!"
    fi
fi

# Cache configuration, routes, and views for production optimization
echo "Caching Laravel configuration, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Run database migrations in production if enabled (useful for auto-deployments)
if [ "${RUN_MIGRATIONS}" = "true" ]; then
    echo "Running database migrations..."
    php artisan migrate --force
fi

echo "Starting Supervisor process manager..."
# Execute the CMD passed to docker run (by default supervisord)
exec "$@"
