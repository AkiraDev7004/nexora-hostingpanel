#!/usr/bin/env bash
# NEXORA Panel - shared shell library (GPL-3.0, derived from HestiaCP)
set -o pipefail
C0=$'\e[0m'; CB=$'\e[1m'; CR=$'\e[31m'; CG=$'\e[32m'; CY=$'\e[33m'; CC=$'\e[36m'; CD=$'\e[90m'
NX_LOG="${NX_LOG:-/var/log/nexora/install.log}"
mkdir -p "$(dirname "$NX_LOG")" 2>/dev/null && touch "$NX_LOG" && chmod 600 "$NX_LOG"
log(){ echo "[$(date -Is)] $*" >>"$NX_LOG"; }
ok(){ printf "  ${CG}[ OK ]${C0} %s\n" "$*"; log "OK $*"; }
warn(){ printf "  ${CY}[WARN]${C0} %s\n" "$*"; log "WARN $*"; }
err(){ printf "  ${CR}[FAIL]${C0} %s\n" "$*"; log "FAIL $*"; }
info(){ printf "  ${CD}%-22s${C0} %s\n" "$1" "$2"; log "INFO $1 $2"; }
logo(){ printf "${CB}${CC}"
cat <<'L'
  _   _ _______  _____  ____      _
 | \ | | ____\ \/ / _ \|  _ \    / \
 |  \| |  _|  \  / | | | |_) |  / _ \
 | |\  | |___ /  \ |_| |  _ <  / ___ \
 |_| \_|_____/_/\_\___/|_| \_\/_/   \_\
L
printf "${C0}${CB}  NEXORA PANEL${C0}\n  ${CD}Simple Hosting. Serious Isolation. Total Control.${C0}\n\n"; }
need_root(){ [ "$(id -u)" -eq 0 ] || { err "Root privileges required"; exit 1; }; }
step(){ printf "\n${CB}[%s/%s]${C0} %s\n" "$1" "$2" "$3"; log "STEP $1/$2 $3"; }
