# Greg ICT Hub LMS — Hostinger Production Deployment Guide

**Target Domain**: [https://lms.gregicthub.com](https://lms.gregicthub.com/)  
**Organization**: Greg & Co. ICT Ltd, Makurdi, Benue State, Nigeria  
**Hostinger SSH Port**: `65002`  
**Hostinger SSH User/IP**: `u842010817@46.202.172.40`  

---

## 1. Architectural Safeguards for Shared Hosting

Hostinger shared hosting environments enforce specific structural constraints:
1. **Document Root Exposure**: The web server serves files directly from `public_html/`. If the entire Laravel project is placed inside `public_html/`, confidential configuration files (`.env`), storage uploads, database seeds, and vendor packages risk direct public exposure.
2. **Directory Separation Strategy**:
   - Application Core: `/home/u842010817/lms_core/` (completely inaccessible from HTTP requests).
   - Public Assets: `/home/u842010817/domains/lms.gregicthub.com/public_html/` (symbolic link to `lms_core/public/`, or contains solely the public index file and pre-compiled assets).
3. **Precompiled Vite Assets**: Hostinger shared hosting does not permit persistent Node.js/Vite daemon processes. Vite assets must be compiled locally with `npm run build` prior to deployment. All JavaScript chunks, stylesheets, and manifest files are already compiled into `public/build/`.

---

## 2. Pre-Deployment Preparation (Local Machine)

### Step 2.1 — Verify Vite Build
Confirm production assets are compiled:
```bash
npm run build
```
Verify the existence of `public/build/manifest.json` and chunk files.

### Step 2.2 — Prepare Clean Deployment Archive
Exclude temporary cache files, `.git/`, and dev dependencies:
```bash
tar --exclude='node_modules' \
    --exclude='.git' \
    --exclude='storage/logs/*.log' \
    --exclude='storage/framework/cache/data/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    -czvf lms_release.tar.gz .
```

---

## 3. Hostinger Server Configuration

### Step 3.1 — Connect via SSH
```bash
ssh -p 65002 u842010817@46.202.172.40
```

### Step 3.2 — Set PHP Version
1. Log in to the Hostinger hPanel.
2. Navigate to **Websites** &rarr; **lms.gregicthub.com** &rarr; **Advanced** &rarr; **PHP Configuration**.
3. Select **PHP 8.2** or **PHP 8.3**.
4. Enable the following PHP extensions:
   - `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `gd`, `zip`.

### Step 3.3 — Create MySQL Database
1. In hPanel, go to **Databases** &rarr; **Management**.
2. Create a new MySQL database:
   - **Database Name**: `u842010817_greglms`
   - **Username**: `u842010817_lmsuser`
   - **Password**: *(Generate strong 24-character password)*

---

## 4. Deploying Application Files

### Step 4.1 — Upload Release Archive
From your local terminal:
```bash
scp -P 65002 lms_release.tar.gz u842010817@46.202.172.40:/home/u842010817/
```

### Step 4.2 — Extract Code into Secure Core Folder
On the remote server (via SSH):
```bash
cd /home/u842010817
mkdir -p lms_core
tar -xzvf lms_release.tar.gz -C lms_core
rm lms_release.tar.gz
```

### Step 4.3 — Link Public Directory
Remove any existing default `public_html` files and point to the Laravel `public` directory:
```bash
# Backup existing default public_html if needed
mv /home/u842010817/domains/lms.gregicthub.com/public_html /home/u842010817/domains/lms.gregicthub.com/public_html_old

# Symlink public directory
ln -s /home/u842010817/lms_core/public /home/u842010817/domains/lms.gregicthub.com/public_html
```

*Note: If Hostinger shared hosting restricts symbolic links for document roots, copy `lms_core/public/*` into `public_html` and edit `index.php` to reference `/home/u842010817/lms_core/vendor/autoload.php` and `/home/u842010817/lms_core/bootstrap/app.php`.*

---

## 5. Production Environment Configuration (`.env`)

Inside `/home/u842010817/lms_core/`:
```bash
cp .env.example .env
nano .env
```

Configure key parameters:
```env
APP_NAME="Greg ICT Hub LMS"
APP_ENV=production
APP_KEY=base64:... # Generate with: php artisan key:generate
APP_DEBUG=false
APP_URL=https://lms.gregicthub.com

LOG_CHANNEL=daily
LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u842010817_greglms
DB_USERNAME=u842010817_lmsuser
DB_PASSWORD=YourSecurePasswordHere

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true

QUEUE_CONNECTION=database
CACHE_STORE=file

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=noreply@lms.gregicthub.com
MAIL_PASSWORD=YourMailPassword
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="noreply@lms.gregicthub.com"
MAIL_FROM_NAME="Greg ICT Hub LMS"
```

---

## 6. Database Initialization and Seeding

Run the production migrations and baseline seeds:
```bash
cd /home/u842010817/lms_core

# Generate application encryption key (if not already set)
php artisan key:generate --force

# Execute database schema
php artisan migrate --force

# Seed roles, permissions, and 14 trade categories
php artisan db:seed --class=RoleAndCategorySeeder --force

# Link storage
php artisan storage:link

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 7. Automated Cron Job Configuration

To ensure background queue processing, assignment due date notifications, and automated database backups execute reliably:

In Hostinger hPanel &rarr; **Advanced** &rarr; **Cron Jobs**:
- **Type**: Custom
- **Command**:
  ```bash
  /usr/bin/php /home/u842010817/lms_core/artisan schedule:run >> /dev/null 2>&1
  ```
- **Interval**: `* * * * *` (Every minute)

---

## 8. SSL & Security Headers

1. In hPanel &rarr; **Security** &rarr; **SSL**, ensure **Let's Encrypt SSL** is active and **Force HTTPS** is enabled.
2. In `lms_core/public/.htaccess`, ensure standard security headers (X-Frame-Options, X-Content-Type-Options, Strict-Transport-Security) are active.
