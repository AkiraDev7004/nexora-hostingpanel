#!/usr/bin/env bash
# NEXORA test suite: syntax, security patterns, and DNS-block functional tests (sandboxed).
# Needs root for the sandbox (/etc/hestiacp is created temporarily if missing).
R="$(cd "$(dirname "$0")/../.." && pwd)"
pass=0
fail=0
t() { if eval "$2" > /dev/null 2>&1; then
	echo "PASS $1"
	pass=$((pass + 1))
else
	echo "FAIL $1"
	fail=$((fail + 1))
fi; }
for f in "$R"/bin/v-nexora-* "$R"/nexora/scripts/*.sh; do t "bash -n $(basename "$f")" "bash -n '$f'"; done
for f in $(cd "$R" && git ls-files 'web/*.php' | grep -E 'nexora|site|docker|mailcow|branding|roles|customers|api-docs|dns-block|dns_block'); do t "php -l $f" "php -l '$R/$f'"; done
# security patterns: every new endpoint must verify CSRF on POST and never pass raw input to exec
for f in "$R"/web/list/{site,docker,dns-block,mailcow,roles,customers}/index.php "$R"/web/add/site/index.php "$R"/web/edit/branding/index.php "$R"/web/nexora/site/index.php; do
	n=$(basename "$(dirname "$f")")
	grep -q '_POST' "$f" && t "csrf $n" "grep -q verify_csrf '$f'"
	t "no raw input in exec $n" "! grep -E 'exec\\(.*\\\$_(GET|POST)\\[' '$f' | grep -v quoteshellarg"
done
t "no plaintext api secret written" "grep -q 'sha256:' '$R/bin/v-add-access-key'"
t "container hardening flags" "grep -q -- '--cap-drop ALL' '$R/bin/v-nexora-site' && grep -q -- '--read-only' '$R/bin/v-nexora-site' && grep -q no-new-privileges '$R/bin/v-nexora-site' && ! grep -v '^#' '$R/bin/v-nexora-site' | grep -q docker.sock"
t "ports bound to localhost" "grep -q '127.0.0.1:\$PORT' '$R/bin/v-nexora-site'"
# functional DNS block tests in sandbox
if [ "$(id -u)" = 0 ]; then
	S=$(mktemp -d)
	mkdir -p "$S/hestia/conf" "$S/hestia/log" "$S/hestia/func" "$S/unbound/unbound.conf.d" "$S/bin"
	cp "$R"/func/*.sh "$S/hestia/func/"
	touch "$S/hestia/conf/hestia.conf"
	created=0
	[ -e /etc/hestiacp ] || {
		mkdir -p /etc/hestiacp
		created=1
	}
	[ -f /etc/hestiacp/hestia.conf ] && cp /etc/hestiacp/hestia.conf "$S/bak"
	echo "HESTIA=$S/hestia" > /etc/hestiacp/hestia.conf
	printf '#!/bin/sh\nexit 0\n' > "$S/bin/unbound-checkconf"
	printf '#!/bin/sh\nexit 0\n' > "$S/bin/systemctl"
	chmod +x "$S"/bin/*
	B="$R/bin/v-nexora-dns-block"
	O=/etc/unbound/unbound.conf.d/nexora.conf
	mkdir -p /etc/unbound/unbound.conf.d
	export PATH="$S/bin:$PATH"
	t "block wildcard" "$B add-block '*.example.com' nxdomain && grep -q 'example.com.\" always_nxdomain' $O"
	t "block sinkhole v4+v6" "$B add-block ads.foo.org sinkhole 192.0.2.1 2001:db8::1 && grep -q 'AAAA 2001:db8::1' $O"
	t "redirect cname" "$B add-redirect a.org CNAME b.net && grep -q 'CNAME b.net.' $O"
	t "reject injection" "! $B add-block 'x.com\";evil' nxdomain"
	t "reject bad ip" "! $B add-redirect a.org A 999.1"
	t "ssrf: http rejected" "! $B add-list l1 http://example.com/list"
	t "ssrf: localhost rejected" "! $B add-list l2 https://localhost/list"
	t "delete rule" "$B del-block ads.foo.org && ! grep -q ads.foo.org $O"
	[ -f "$S/bak" ] && cp "$S/bak" /etc/hestiacp/hestia.conf || rm -f /etc/hestiacp/hestia.conf
	[ $created = 1 ] && rm -rf /etc/hestiacp
	rm -f "$O"
	rm -rf "$S"
fi
echo "---- $pass passed, $fail failed"
[ $fail = 0 ]
