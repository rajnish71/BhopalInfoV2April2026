#!/bin/bash

echo "📁 Bhopal.info Dev Workflow"
echo "----------------------------------"

# Ensure we are in correct branch
CURRENT_BRANCH=$(git branch --show-current)
echo "🔀 Current branch: $CURRENT_BRANCH"

# Pull latest changes
echo "⬇️ Pulling latest changes..."
git pull origin $CURRENT_BRANCH

# Show status
echo "📊 Current changes:"
git status

# Ask for confirmation
read -p "👉 Continue with commit? (y/n): " confirm
if [ "$confirm" != "y" ]; then
  echo "❌ Aborted"
  exit 1
fi

# Add files
echo "➕ Adding files..."
git add .

# Commit message
read -p "📝 Enter commit message: " msg

# Commit
git commit -m "$msg"

# Push
echo "�� Pushing to origin/$CURRENT_BRANCH..."
git push origin $CURRENT_BRANCH

echo "✅ Done successfully!"
