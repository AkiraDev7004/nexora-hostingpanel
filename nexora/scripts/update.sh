#!/usr/bin/env bash
. "$(dirname "$(readlink -f "$0")")/lib.sh"
need_root
D="$(dirname "$(readlink -f "$0")")"
ROOT="$(readlink -f "$D/../..")"
logo
step 1 2 "Updating sources"
git -C "$ROOT" pull --ff-only >> "$NX_LOG" 2>&1 && ok "Sources updated" || warn "git pull skipped"
step 2 2 "Updating system packages (hestia only)"
apt-get -qq update && apt-get -y -qq install --only-upgrade hestia-nginx hestia-php >> "$NX_LOG" 2>&1
"$D/sync-core.sh"
