#!/bin/bash
set -e

echo "=== Starting Deployment ==="
echo "PORT: ${PORT:-8000}"
echo "DB_HOST: ${DB_HOST}"

# Wait for database
echo "=== Waiting for Database ==="
until php artisan db:show --database=mysql 2>/dev/null | grep -q "Connection"; do
    echo "Database not ready - sleeping 2s"
    sleep 2
done
echo "✅ Database is ready!"

# Run migrations
echo "=== Running Migrations ==="
php artisan migrate:fresh --force --seed

# Create storage link
echo "=== Creating Storage Link ==="
php artisan storage:link

# Start server in background
echo "=== Starting Laravel Server ==="
php artisan serve --host=0.0.0.0 --port=${PORT:-8000} &
SERVER_PID=$!

# Wait for server to bind port
echo "=== Waiting for server to be ready ==="
sleep 5

# Health check loop
for i in {1..15}; do
    if curl -f http://localhost:${PORT:-8000}/api/health > /dev/null 2>&1; then
        echo "✅ Server is healthy and ready!"
        break
    fi
    echo "⏳ Server starting... attempt $i/15"
    sleep 2
done

# Check if server process is still running
if ! kill -0 $SERVER_PID 2>/dev/null; then
    echo "❌ Server process died!"
    exit 1
fi

echo "🚀 Application is now accepting traffic"
wait $SERVER_PID