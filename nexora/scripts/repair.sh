#!/usr/bin/env bash
. "$(dirname "$(readlink -f "$0")")/lib.sh"; need_root; logo
for s in nginx hestia php*-fpm mariadb named exim4 dovecot docker; do
  for u in $(systemctl list-unit-files "$s.service" --no-legend 2>/dev/null | awk '{print $1}'); do
    systemctl is-enabled -q "$u" || continue
    systemctl is-active -q "$u" && ok "$u" || { systemctl restart "$u" && ok "$u restarted" || err "$u failed"; }
  done; done
[ -x /usr/local/hestia/bin/v-rebuild-all ] && /usr/local/hestia/bin/v-rebuild-all >>"$NX_LOG" 2>&1 && ok "Configs rebuilt"
