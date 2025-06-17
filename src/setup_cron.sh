#!/bin/bash

# Get absolute path to cron.php
SCRIPT_PATH="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/cron.php"

# Create a cron job entry that runs every hour
CRON_JOB="0 * * * * php $SCRIPT_PATH"

# Check if job already exists to avoid duplicates
(crontab -l 2>/dev/null | grep -v -F "$SCRIPT_PATH" ; echo "$CRON_JOB") | crontab -

echo "CRON job installed successfully to run cron.php every hour."
