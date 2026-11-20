#!/usr/bin/env bash
# Usage: restore.sh <user> <backup-file-name>
. "$(dirname "$(readlink -f "$0")")/lib.sh"; need_root
[ $# -eq 2 ] || { echo "Usage: $0 <user> <backup.tar>"; exit 1; }
[[ "$1" =~ ^[a-z0-9_-]+$ && "$2" =~ ^[A-Za-z0-9._-]+$ ]] || { err "Invalid arguments"; exit 1; }
/usr/local/hestia/bin/v-restore-user "$1" "$2" >>"$NX_LOG" 2>&1 && ok "Restored $1" || err "Restore failed"
