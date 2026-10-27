# Greg ICT Hub LMS — Backup & Disaster Recovery Manual

**Target Domain**: [https://lms.gregicthub.com](https://lms.gregicthub.com/)  
**Organization**: Greg & Co. ICT Ltd, Makurdi, Benue State, Nigeria  

---

## 1. Backup Strategy Overview

A reliable disaster recovery strategy for Greg ICT Hub LMS requires two components:
1. **Relational Database Dumps**: MySQL schema and data (including users, enrollments, rubrics, submissions, and grades).
2. **Private File Storage**: Teacher uploaded resources (`storage/app/private/resources/`) and student assignment attachments (`storage/app/private/submissions/`).

---

## 2. Automated Daily Database Backup Script

Create a script on the server at `/home/u842010817/scripts/backup_lms.sh`:

```bash
#!/usr/bin/env bash
set -e

BACKUP_DIR="/home/u842010817/backups/db"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
DB_NAME="u842010817_greglms"
DB_USER="u842010817_lmsuser"
DB_PASS="YourSecureDatabasePassword"

mkdir -p "${BACKUP_DIR}"

# 1. Dump database with gzip compression
mysqldump -u "${DB_USER}" -p"${DB_PASS}" --single-transaction --quick --routines "${DB_NAME}" | gzip > "${BACKUP_DIR}/lms_db_${TIMESTAMP}.sql.gz"

# 2. Prune backups older than 30 days
find "${BACKUP_DIR}" -type f -name "*.sql.gz" -mtime +30 -delete

echo "Database backup completed: ${BACKUP_DIR}/lms_db_${TIMESTAMP}.sql.gz"
```

Make executable:
```bash
chmod +x /home/u842010817/scripts/backup_lms.sh
```

Add to Hostinger Cron Jobs:
- Run daily at 02:00 AM: `0 2 * * * /home/u842010817/scripts/backup_lms.sh > /dev/null 2>&1`

---

## 3. Storage Archive Script (Weekly)

Create `/home/u842010817/scripts/backup_storage.sh`:

```bash
#!/usr/bin/env bash
set -e

BACKUP_DIR="/home/u842010817/backups/storage"
TIMESTAMP=$(date +"%Y%m%d")
SOURCE_DIR="/home/u842010817/lms_core/storage/app/private"

mkdir -p "${BACKUP_DIR}"

tar -czvf "${BACKUP_DIR}/lms_private_files_${TIMESTAMP}.tar.gz" -C "${SOURCE_DIR}" .

# Retain 8 weekly backups (2 months)
find "${BACKUP_DIR}" -type f -name "*.tar.gz" -mtime +60 -delete
```

Add to Hostinger Cron Jobs:
- Run weekly on Sunday at 03:00 AM: `0 3 * * 0 /home/u842010817/scripts/backup_storage.sh > /dev/null 2>&1`

---

## 4. Disaster Recovery & Restoration Procedures

### Scenario A — Database Corruption or Accidental Deletion
1. Put application into maintenance mode:
   ```bash
   php /home/u842010817/lms_core/artisan down --render="errors::503"
   ```
2. Locate the most recent valid backup:
   ```bash
   ls -lt /home/u842010817/backups/db/
   ```
3. Restore using gunzip and mysql:
   ```bash
   gunzip < /home/u842010817/backups/db/lms_db_YYYYMMDD_HHMMSS.sql.gz | mysql -u u842010817_lmsuser -p u842010817_greglms
   ```
4. Clear cache and bring application up:
   ```bash
   php /home/u842010817/lms_core/artisan cache:clear
   php /home/u842010817/lms_core/artisan up
   ```

### Scenario B — Full Server Migration
1. Provision new environment with PHP 8.2+ and MySQL.
2. Clone `lms_core` or extract release archive.
3. Import database dump.
4. Extract private storage backup into `storage/app/private/`.
5. Run `php artisan storage:link`, `php artisan config:cache`, `php artisan route:cache`.
