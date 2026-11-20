#!/usr/bin/env bash
# NEXORA mode migration assistant: classic <-> docker. Never deletes site data.
. "$(dirname "$(readlink -f "$0")")/lib.sh"
need_root
logo
D="$(dirname "$(readlink -f "$0")")"
. /etc/nexora/nexora.conf 2> /dev/null
CUR=${MODE:-classic}
TARGET=${1:-}
[ -z "$TARGET" ] && { [ "$CUR" = docker ] && TARGET=classic || TARGET=docker; }
[[ "$TARGET" =~ ^(docker|classic)$ ]] || {
	err "Usage: $0 [docker|classic]"
	exit 1
}
info "Current mode" "$CUR"
info "Target mode" "$TARGET"
if [ "$TARGET" = docker ]; then "$D/docker-check.sh" || {
	err "Docker not usable - staying in $CUR mode"
	exit 1
}; fi
B=/usr/local/hestia/bin
printf "\n${CB}Websites to migrate${C0}\n"
for u in $($B/v-list-users plain | cut -f1); do $B/v-nexora-site list "$u" | awk -v u="$u" '{print "    " u "  " $1 "  (" $2 ", " $3 ")"}'; done
read -rp "  Backup is recommended (backup.sh). Proceed? [y/N] " a
[[ "$a" =~ ^[yY] ]] || exit 0
sed -i "s/^MODE=.*/MODE=$TARGET/; s/^DOCKER=.*/DOCKER=$([ $TARGET = docker ] && echo yes || echo no)/" /etc/nexora/nexora.conf
fails=0
for u in $($B/v-list-users plain | cut -f1); do
	for d in $($B/v-nexora-site list "$u" | awk '{print $1}'); do
		if $B/v-nexora-site migrate "$u" "$d" >> "$NX_LOG" 2>&1; then ok "$d"; else
			err "$d (kept on old runtime, see $NX_LOG)"
			fails=$((fails + 1))
		fi
	done
done
[ $fails = 0 ] && ok "Migration to $TARGET complete" || warn "$fails site(s) could not be migrated. Re-run after fixing."
