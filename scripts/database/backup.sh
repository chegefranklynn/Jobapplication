#!/bin/bash
# ShellCheck-compliant backup script

TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_DIR="/var/backups/jobapplication"
LOG_FILE="/var/log/backup.log"

# Load environment variables
source /opt/jobapplication/.env || exit 1

# Create backup directory
mkdir -p "$BACKUP_DIR" || {
    echo "[$(date)] Failed to create backup directory" >> "$LOG_FILE"
    exit 1
}

# PostgreSQL Backup
echo "[$(date)] Starting PostgreSQL backup" >> "$LOG_FILE"
if pg_dump -U "$DB_USER" -d "$DB_NAME" | gzip > "$BACKUP_DIR/pg_backup_$TIMESTAMP.sql.gz" 2>> "$LOG_FILE"
then
    # Verify backup
    if gzip -t "$BACKUP_DIR/pg_backup_$TIMESTAMP.sql.gz"
    then
        echo "[$(date)] Backup successful: pg_backup_$TIMESTAMP.sql.gz" >> "$LOG_FILE"
    else
        echo "[$(date)] Backup verification failed!" >> "$LOG_FILE"
        exit 1
    fi
else
    echo "[$(date)] Backup process failed!" >> "$LOG_FILE"
    exit 1
fi