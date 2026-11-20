#!/usr/bin/env bash
# NEXORA Installation Wizard (GPL-3.0). Wraps the HestiaCP installer core.
D="$(dirname "$(readlink -f "$0")")"
ROOT="$(readlink -f "$D/../..")"
. "$D/lib.sh"
need_root
clear 2> /dev/null
logo
printf "${CB}NEXORA Installation Wizard${C0}\n\n"
eval "$("$D/system-check.sh" --env)"
"$D/system-check.sh"
[ "$SUPPORTED" = yes ] || {
	err "Aborting: unsupported OS"
	exit 1
}
printf "\n${CB}Docker check${C0}\n"
if "$D/docker-check.sh"; then
	DOCKER=yes
	MODE=docker
else
	DOCKER=no
	MODE=classic
	if [ "$VIRT" = kvm ] || [ "$VIRT" = none ] || [ "$VIRT" = vmware ]; then
		read -rp "  Install Docker Engine and re-test? [Y/n] " a
		if [[ ! "$a" =~ ^[nN] ]]; then
			apt-get -qq update && apt-get -y -qq install docker.io >> "$NX_LOG" 2>&1 && systemctl enable --now docker >> "$NX_LOG" 2>&1
			"$D/docker-check.sh" && {
				DOCKER=yes
				MODE=docker
			}
		fi
	fi
fi
[[ "$PROXMOX" == lxc* && $DOCKER = no ]] && warn "For the best Docker compatibility, we recommend running NEXORA inside a dedicated Proxmox VM."
[ $MODE = classic ] && warn "Docker is not available on this system. NEXORA will run in Classic Hosting Mode."
printf "\n${CB}Installation type${C0}\n  [1] Complete Installation\n  [2] Minimal Installation\n  [3] Custom Installation\n  [4] Update Existing Installation\n  [5] Repair Installation\n  [6] Uninstall NEXORA\n"
read -rp "  Select [1-6]: " sel
case "$sel" in 4) exec "$D/update.sh" ;; 5) exec "$D/repair.sh" ;; 6) exec "$D/uninstall.sh" ;; esac
declare -A C=([core]=yes [nginx]=yes [php]=yes [mariadb]=yes [postgresql]=no [dns]=yes [mail]=yes [roundcube]=yes [mailcow]=no [letsencrypt]=yes [firewall]=yes [node]=yes [python]=yes)
[ "$sel" = 2 ] && C+=([dns]=no [mail]=no [roundcube]=no [node]=no [python]=no)
if [ "$sel" = 3 ]; then for k in nginx php node python mariadb postgresql dns mail roundcube mailcow letsencrypt firewall; do
	if [ $k = mailcow ] && [ $DOCKER = no ]; then
		printf "  ${CD}%-12s not available (requires Docker)${C0}\n" $k
		continue
	fi
	read -rp "  $(printf '%-12s' $k) [${C[$k]}] (y/n): " a
	[[ "$a" =~ ^[yY] ]] && C[$k]=yes
	[[ "$a" =~ ^[nN] ]] && C[$k]=no
done; fi
[ $DOCKER = no ] && C[mailcow]=no
[ "${C[mailcow]}" = yes ] && C[mail]=no
read -rp "  Panel hostname (FQDN): " HOST
[[ "$HOST" =~ ^[a-zA-Z0-9.-]+$ ]] || {
	err "Invalid hostname"
	exit 1
}
read -rp "  Admin e-mail: " EMAIL
[[ "$EMAIL" =~ ^[^@[:space:]]+@[^@[:space:]]+$ ]] || {
	err "Invalid e-mail"
	exit 1
}
printf "\n${CB}NEXORA Installation Summary${C0}\n"
info "Operating System" "$OS_NAME"
info "Virtualization" "$VTYPE"
info "Docker Support" "$([ $DOCKER = yes ] && echo Available || echo 'Not Available')"
info "Installation Mode" "$([ $MODE = docker ] && echo 'Full Docker Mode' || echo 'Classic Hosting Mode')"
for k in "${!C[@]}"; do [ "${C[$k]}" = yes ] && echo "    * $k"; done
read -rp "  Proceed? [y/N] " a
[[ "$a" =~ ^[yY] ]] || exit 0
yn() { [ "${C[$1]}" = yes ] && echo yes || echo no; }
installer="$ROOT/install/hst-install.sh"
PASS="$(head -c 18 /dev/urandom | base64 | tr -dc A-Za-z0-9)"
step 1 4 "Installing hosting core (nginx/php-fpm/db/dns/mail)"
bash "$installer" --interactive no --force --hostname "$HOST" --email "$EMAIL" --username nexadmin --password "$PASS" \
	--apache no --phpfpm "$(yn php)" --multiphp "$(yn php)" --mysql "$(yn mariadb)" --postgresql "$(yn postgresql)" \
	--named "$(yn dns)" --exim "$(yn mail)" --dovecot "$(yn mail)" --clamav no --spamassassin no \
	--iptables "$(yn firewall)" --fail2ban "$(yn firewall)" --api yes >> "$NX_LOG" 2>&1 || {
	err "Core install failed. See $NX_LOG; run repair.sh"
	exit 1
}
ok "Core installed"
step 2 4 "Applying NEXORA configuration"
install -d -m 750 /usr/local/nexora /etc/nexora
cp -r "$ROOT/nexora/." /usr/local/nexora/
"$D/sync-core.sh" || {
	err "Applying NEXORA files failed"
	exit 1
}
# API for all users; every key stays scoped to its owner by the API user-argument check
/usr/local/hestia/bin/v-change-sys-config-value API_SYSTEM 2
printf 'MODE=%s\nDOCKER=%s\nNODE=%s\nPYTHON=%s\nMAILCOW=%s\n' $MODE $DOCKER "$(yn node)" "$(yn python)" "$(yn mailcow)" > /etc/nexora/nexora.conf
chmod 640 /etc/nexora/nexora.conf
/usr/local/hestia/bin/v-change-sys-config-value THEME nexora-oled
/usr/local/hestia/bin/v-change-sys-config-value APP_NAME "NEXORA Panel"
step 3 4 "Runtimes"
[ "$(yn node)" = yes ] && apt-get -y -qq install nodejs npm >> "$NX_LOG" 2>&1 && ok "Node.js"
apt-get -y -qq install unbound >> "$NX_LOG" 2>&1 && mkdir -p /etc/unbound/unbound.conf.d && printf "server:\n  interface: 127.0.0.1@5335\n  access-control: 127.0.0.0/8 allow\n" > /etc/unbound/unbound.conf.d/nexora.conf && systemctl restart unbound >> "$NX_LOG" 2>&1 && ok "Unbound resolver (127.0.0.1:5335, local only)"
[ "$(yn python)" = yes ] && apt-get -y -qq install python3 python3-venv >> "$NX_LOG" 2>&1 && ok "Python"
if [ "$(yn mailcow)" = yes ]; then git clone -q https://github.com/mailcow/mailcow-dockerized /opt/mailcow-dockerized >> "$NX_LOG" 2>&1 && ok "Mailcow fetched to /opt/mailcow-dockerized (run generate_config.sh)"; fi
step 4 4 "Final verification"
for s in nginx hestia; do systemctl is-active -q $s && ok "$s active" || err "$s inactive"; done
IP="$(echo $IPS | awk '{print $1}')"
umask 077
printf 'user=nexadmin\npassword=%s\n' "$PASS" > /root/.nexora-admin
printf "\n${CG}${CB}NEXORA PANEL INSTALLED SUCCESSFULLY${C0}\n"
info "Panel URL" "https://${IP}:8083"
info "Installation Mode" "$MODE"
info "Docker" "$DOCKER"
info "Admin account" "nexadmin (password in /root/.nexora-admin, mode 600)"
