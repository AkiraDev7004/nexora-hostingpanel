# NEXORA Panel
Simple Hosting. Serious Isolation. Total Control.

Fork of HestiaCP (GPL-3.0, see ../LICENSE). Original copyright notices retained.

## Done (Phase 2/3 start)
- `web/css/src/themes/nexora-oled.css`: OLED theme (#000/#050505/#0A0A0A/#202020), default for new installs.
- `nexora/scripts/`: install, system-check, docker-check (real test container), update, repair, backup, restore, uninstall.
  Installer wraps the HestiaCP core installer; Classic mode = HestiaCP stack (nginx, php-fpm pools, per-user Linux accounts).

## Done (Phase 4/5/8/9 parts)
- `bin/v-nexora-site`: per-website runtime. Docker mode = own container + own bridge network (ICC off),
  non-root, cap-drop ALL, no-new-privileges, read-only rootfs, CPU/RAM/PID limits, port on 127.0.0.1 only.
  Classic mode = Hestia php-fpm pool / hardened systemd unit (Node/Python). Types: php, wordpress, node, python, static, proxy.
  IP must be configured on the host. Optional DNS zone, mail domain, Let's Encrypt.
- Web UI: `/list/site/` (Websites, start/stop/restart/rebuild/delete/logs, live CPU/RAM), `/add/site/` (Create Website wizard).
- `bin/v-nexora-dns-block` + `/list/dns-block/`: blocking (NXDOMAIN/0.0.0.0/sinkhole, IPv4/6, wildcard),
  DNS overrides (A/AAAA/CNAME/TTL), URL blocklists (https + public hosts only, SSRF-protected), resolver ACL (Unbound).
- API secrets stored as SHA-256 hash, shown once; expiry, disable flag, last-used tracking.
- Branding: APP_NAME = "NEXORA Panel" in footer, 2FA issuer, server pages (GPL notice kept).

## Open (not yet implemented)
Mailcow management UI, reseller role, branding settings page (logo/colors), Docker images/networks pages, full sidebar restructure,
mode migration assistant, API docs page, automated tests. Not yet tested on a real server.
