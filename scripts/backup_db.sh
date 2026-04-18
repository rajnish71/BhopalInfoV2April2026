#!/bin/bash
BACKUP_DIR="/var/www/bhopal-admin-core/shared/backups"
mkdir -p $BACKUP_DIR
FILENAME="db_backup_$(date +%Y%m%d_%H%M%S).sql"
# Assuming DB credentials from .env or provided
# Use a clever way to get DB credentials or just hardcode if known
mysqldump -u bhopal_admin -pMeera2103@ bhopal_admin_core > $BACKUP_DIR/$FILENAME
# Retain only last 7 days
find $BACKUP_DIR -type f -name "*.sql" -mtime +7 -delete