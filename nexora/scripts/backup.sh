#!/usr/bin/env bash
# Usage: backup.sh [user|--all]  -> backups stored in /backup (root-only)
. "$(dirname "$(readlink -f "$0")")/lib.sh"; need_root
B=/usr/local/hestia/bin; users="${1:---all}"; [ "$users" = --all ] && users="$($B/v-list-users plain | cut -f1)"
for u in $users; do $B/v-backup-user "$u" >>"$NX_LOG" 2>&1 && ok "Backup $u" || err "Backup $u"; done
tar czf /backup/nexora-config-$(date +%F).tar.gz /etc/nexora /usr/local/hestia/conf 2>>"$NX_LOG" && chmod 600 /backup/nexora-config-*.tar.gz && ok "Config backup"
