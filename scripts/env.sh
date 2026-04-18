#!/bin/bash

# Environment Switcher for Bhopal.info

ENV_TARGET=$1

if [ -z "$ENV_TARGET" ]; then
    echo "❌ Usage: ./scripts/env.sh <dev|staging|prod>"
    exit 1
fi

ENV_FILE=".env.$ENV_TARGET"

if [ ! -f "$ENV_FILE" ]; then
    echo "❌ Environment file $ENV_FILE does not exist!"
    exit 1
fi

echo "🔄 Switching to $ENV_TARGET environment..."

# Safely copy the targeted env file to active .env
cp "$ENV_FILE" .env

# Clear caches and configurations
echo "🧹 Clearing application caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Print active environment
ACTIVE_ENV=$(php artisan env)
echo "✅ Environment check: $ACTIVE_ENV"
echo "🚀 Environment successfully switched to $ENV_TARGET"
