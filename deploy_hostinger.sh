#!/usr/bin/env bash
# ==============================================================================
# Greg ICT Hub LMS — Hostinger Shared Hosting Deployment Automation Script
# Production Target: https://lms.gregicthub.com
# Organization: Greg & Co. ICT Ltd, Makurdi, Benue State, Nigeria
# SSH Info: ssh -p 65002 u842010817@46.202.172.40
# ==============================================================================

set -e

echo ">>> [1/7] Initializing Greg ICT Hub LMS Deployment..."

# Define application paths on Hostinger
# Recommended architecture: Keep core Laravel codebase OUTSIDE public_html
REMOTE_HOME="/home/u842010817"
CORE_DIR="${REMOTE_HOME}/lms_core"
PUBLIC_DIR="${REMOTE_HOME}/domains/lms.gregicthub.com/public_html"

echo "Core Directory:   ${CORE_DIR}"
echo "Public Directory: ${PUBLIC_DIR}"

# Navigate to application core
cd "${CORE_DIR}"

# Put application into maintenance mode
echo ">>> [2/7] Enabling maintenance mode..."
php artisan down --render="errors::503" --secret="greg-deploy-bypass-2026" || true

# Install Composer production dependencies (no dev packages)
echo ">>> [3/7] Installing production PHP dependencies via Composer..."
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Run database migrations
echo ">>> [4/7] Running database migrations..."
php artisan migrate --force

# Seed base roles and trade categories if empty
echo ">>> [5/7] Ensuring essential roles and TVET categories are populated..."
php artisan db:seed --class=RoleAndCategorySeeder --force

# Clear and rebuild production configuration and route caches
echo ">>> [6/7] Optimizing Laravel caches (config, routes, views, events)..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Ensure storage link is active
php artisan storage:link || true

# Bring application out of maintenance mode
echo ">>> [7/7] Bringing application out of maintenance mode..."
php artisan up

echo "=============================================================================="
echo ">>> Deployment completed successfully for https://lms.gregicthub.com !"
echo "=============================================================================="
