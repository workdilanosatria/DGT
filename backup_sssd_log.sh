#!/bin/bash

BACKUP_FILE=/var/log/sssd/backup-sssd-log_$(date +%Y-%m-%d_%H-%M-%S)
ACTUAL_FILE=/var/log/messages3
REMOVE_BACKUP_FILE=true
REMOVE_N_DAYS=90

cp $ACTUAL_FILE $BACKUP_FILE
echo "Backing up " $ACTUAL_FILE "to" $BACKUP_FILE
if test -f "$BACKUP_FILE"; then
    echo "Truncating " $ACTUAL_FILE
    truncate -s 0 $ACTUAL_FILE
fi

if [ "$REMOVE_BACKUP_FILE" == "true" ]; then
    echo "Removing backup file older than" $REMOVE_N_DAYS "days:"
    find /var/log/sssd ! -name -prune -type f -mtime +$REMOVE_N_DAYS
    find /var/log/sssd ! -name -prune -type f -mtime +$REMOVE_N_DAYS -exec rm -f {} +
fi