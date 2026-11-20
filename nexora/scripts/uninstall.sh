#!/usr/bin/env bash
. "$(dirname "$(readlink -f "$0")")/lib.sh"; need_root; logo
warn "This removes the NEXORA panel. Website data in /home and /backup is NOT deleted."
read -rp "  Type UNINSTALL to continue: " a; [ "$a" = UNINSTALL ] || exit 0
systemctl disable --now hestia >>"$NX_LOG" 2>&1
apt-get -y -qq remove hestia hestia-nginx hestia-php >>"$NX_LOG" 2>&1
rm -rf /usr/local/nexora /etc/nexora; ok "NEXORA removed (data preserved)"
