#!/bin/bash

echo "📁 Bhopal.info Dev Workflow Activation"
echo "--------------------------------------"

# 1. Switch to dev environment
echo "🔄 1. Switching to dev environment..."
./scripts/env.sh dev

if [ $? -ne 0 ]; then
    echo "❌ Failed to switch to dev environment."
    exit 1
fi

# Ensure we are in correct branch
CURRENT_BRANCH=$(git branch --show-current)
echo "🔀 Current branch: $CURRENT_BRANCH"

# 2. Pull latest code from repository
echo "⬇️ 2. Pulling latest code from origin/$CURRENT_BRANCH..."
git pull origin "$CURRENT_BRANCH"

# 3. Run migrations
echo "🗄️ 3. Running database migrations..."
php artisan migrate --force

# 4. Prepare system for development
echo "🛠️ 4. Preparing system for development..."
composer install --no-interaction
npm install
npm run build

echo "✅ Dev environment is fully prepared and active!"
