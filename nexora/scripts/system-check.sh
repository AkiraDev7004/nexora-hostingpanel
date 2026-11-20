#!/usr/bin/env bash
# Detects OS, virtualization and hardware. Prints KEY=VALUE with --env.
. "$(dirname "$(readlink -f "$0")")/lib.sh"
. /etc/os-release 2>/dev/null
OS_ID="${ID:-unknown}"; OS_VER="${VERSION_ID:-?}"; OS_NAME="${PRETTY_NAME:-unknown}"
VIRT="$(systemd-detect-virt 2>/dev/null || echo none)"
PROXMOX=no
if [ "$VIRT" = lxc ] && { grep -qa 'container=lxc' /proc/1/environ 2>/dev/null || [ -d /proc/sys/fs/binfmt_misc ]; }; then
  grep -qi pve /proc/version 2>/dev/null && PROXMOX=lxc
fi
[ "$VIRT" = kvm ] && grep -qi -e qemu -e proxmox /sys/class/dmi/id/sys_vendor /sys/class/dmi/id/product_name 2>/dev/null && PROXMOX=vm
[ "$VIRT" = lxc ] && [ "$PROXMOX" = no ] && PROXMOX=lxc-maybe
case "$VIRT" in none) VTYPE="Physical server";; kvm|qemu) VTYPE="KVM VM";; vmware) VTYPE="VMware VM";; lxc*) VTYPE="LXC container";; *) VTYPE="$VIRT";; esac
CPU="$(nproc)"; RAM_MB="$(awk '/MemTotal/{print int($2/1024)}' /proc/meminfo)"
DISK_GB="$(df -BG --output=avail / | tail -1 | tr -dc 0-9)"
KERNEL="$(uname -r)"; ARCH="$(uname -m)"
IPS="$(ip -o addr show scope global 2>/dev/null | awk '{split($4,a,"/");print a[1]}' | xargs)"
SUPPORTED=no
{ [ "$OS_ID" = debian ] && [[ "$OS_VER" =~ ^(11|12|13)$ ]]; } || { [ "$OS_ID" = ubuntu ] && [[ "$OS_VER" =~ ^(22.04|24.04)$ ]]; } && SUPPORTED=yes
if [ "$1" = --env ]; then
  for v in OS_ID OS_VER OS_NAME VIRT VTYPE PROXMOX CPU RAM_MB DISK_GB KERNEL ARCH IPS SUPPORTED; do printf '%s=%q\n' "$v" "${!v}"; done; exit 0
fi
printf "${CB}System check${C0}\n"
info "Operating System" "$OS_NAME"; info "Kernel" "$KERNEL"; info "Architecture" "$ARCH"
info "Virtualization" "$VTYPE"; info "Proxmox" "$PROXMOX"
info "CPU cores" "$CPU"; info "RAM" "${RAM_MB} MB"; info "Free disk (/)" "${DISK_GB} GB"
info "IP addresses" "${IPS:-none}"
[ "$SUPPORTED" = yes ] && ok "Supported OS" || err "Unsupported OS (Debian 11-13, Ubuntu 22.04/24.04)"
[ "$RAM_MB" -ge 1000 ] && ok "RAM >= 1 GB" || warn "Less than 1 GB RAM"
[ "${DISK_GB:-0}" -ge 10 ] && ok "Disk >= 10 GB" || warn "Less than 10 GB free disk"
[ "$ARCH" = x86_64 ] || [ "$ARCH" = aarch64 ] && ok "Architecture supported" || err "Architecture not supported"
getent hosts deb.debian.org >/dev/null && ok "DNS/network reachable" || warn "Network/DNS check failed"
