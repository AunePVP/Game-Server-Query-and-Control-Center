#!/bin/bash
# Project: Game-Server-Query-and-Control-Center
# Author: Leo Muehlhaeusler
# Contributors: https://github.com/AunePVP/Game-Server-Query-and-Control-Center/graphs/contributors
# Documentation: https://github.com/AunePVP/Game-Server-Query-and-Control-Center/wiki
# Website: https://tracker.iguaserver.de
exec {lock_fd}>/var/lock/gsqloglock"$1" || exit 1
flock -n "$lock_fd" || { echo "Running..." >&2; exit 1; }
end=$((SECONDS+60))
while [ $SECONDS -lt $end ]; do
  rsync -avrt --delete --rsh="ssh -i '$2' -p '$3'" "$4"@"$5":"$6" "$7"
  sleep 3
  echo ""
  :
done
flock -u "$lock_fd"
