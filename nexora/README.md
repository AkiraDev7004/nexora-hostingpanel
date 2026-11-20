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

## Done (rest)

- Docker Management (`/list/docker/`): containers, images (delete/prune), networks, live resource usage.
- Branding (`/edit/branding/`): name, title, footer, description, login text, mail sender, accent color,
  support/docs URL, logo/login logo/favicon upload (PNG/JPEG/WEBP only, 1 MB). License notice stays.
- Mailcow (`/list/mailcow/`, "OPSEC"): install central instance, connect API, domains/mailboxes/aliases/passwords.
  Disabled in Classic mode. Postfix/Dovecot/Roundcube = "Kein OPSEC" (HestiaCP mail).
- Roles (`/list/roles/`) Super Admin/Admin/Reseller/User; resellers manage own customers (`/list/customers/`),
  ownership + allowed packages checked server side.
- Mode migration: `v-nexora-site migrate`, `nexora/scripts/migrate-mode.sh` (data never deleted, rollback per site).
- API docs (`/list/api-docs/`) with cURL/Python/PHP/JS; API permission groups `nexora-websites`, `nexora-admin`.
- `nexora/scripts/sync-core.sh`: applies NEXORA core over the HestiaCP base (hestia package held; update.sh re-syncs).
- Tests: `sudo nexora/tests/run.sh` (syntax, security patterns, DNS-block sandbox) + GitHub Actions.

## Known limits

- Not yet verified on a real server end-to-end.
- Reseller cannot "log in as" customers yet. `*.domain` blocking also blocks the apex (Unbound semantics).
- Existing plaintext API keys stay valid until re-created.
