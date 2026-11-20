# NEXORA Panel
Simple Hosting. Serious Isolation. Total Control.

Fork of HestiaCP (GPL-3.0, see ../LICENSE). Original copyright notices retained.

## Done (Phase 2/3 start)
- `web/css/src/themes/nexora-oled.css`: OLED theme (#000/#050505/#0A0A0A/#202020), default for new installs.
- `nexora/scripts/`: install, system-check, docker-check (real test container), update, repair, backup, restore, uninstall.
  Installer wraps the HestiaCP core installer; Classic mode = HestiaCP stack (nginx, php-fpm pools, per-user Linux accounts).

## Open (not yet implemented)
Per-website Docker containers, website wizard (Node/Python/Proxy), DNS blocking/resolver, Mailcow integration,
API key management (hashed), RBAC/reseller roles, branding UI, full sidebar restructure.
