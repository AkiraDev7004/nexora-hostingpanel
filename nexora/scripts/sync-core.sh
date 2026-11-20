#!/usr/bin/env bash
# Applies NEXORA's modified core (bin, func, web, templates, api groups) on top of the
# installed HestiaCP base. The hestia package is held so apt does not overwrite it;
# update.sh pulls the NEXORA repo and re-runs this script.
. "$(dirname "$(readlink -f "$0")")/lib.sh"
need_root
D="$(dirname "$(readlink -f "$0")")"
ROOT="$(readlink -f "$D/../..")"
H=/usr/local/hestia
[ -d "$H/bin" ] || {
	err "HestiaCP base not installed"
	exit 1
}
command -v rsync > /dev/null || apt-get -y -qq install rsync >> "$NX_LOG" 2>&1
rsync -a "$ROOT/bin/" "$H/bin/" && rsync -a "$ROOT/func/" "$H/func/" || exit 1
rsync -a --exclude 'css/src' --exclude 'js/src' "$ROOT/web/" "$H/web/" || exit 1
install -d "$H/data/api" && install -m 644 "$ROOT"/install/common/api/nexora-* "$H/data/api/"
install -d "$H/data/templates/web/nginx/php-fpm" && install -m 644 "$ROOT"/install/deb/templates/web/nginx/php-fpm/nexora-* "$H/data/templates/web/nginx/php-fpm/"
install -d -m 750 /usr/local/nexora && rsync -a "$ROOT/nexora/" /usr/local/nexora/
apt-mark hold hestia > /dev/null 2>&1
ok "NEXORA core applied"
