#!/bin/sh

set -e

cat <<- 'EOF' | crontab -
   0 */12 * * * /site/scripts/update-events.sh
EOF

crond -b -L /site/logs/cron.log

/site/scripts/migrate-db.sh
/start.sh
