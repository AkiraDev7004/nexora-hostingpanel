#!/usr/bin/env bash
# Real Docker capability test. Exit 0 = Docker usable, 1 = not usable.
. "$(dirname "$(readlink -f "$0")")/lib.sh"
fail=0; q="${1:-}"
say(){ [ "$q" = --quiet ] || "$@"; }
[ -d /sys/fs/cgroup ] && say ok "cgroups present ($(stat -fc %T /sys/fs/cgroup))" || { say err "cgroups missing"; fail=1; }
for ns in pid net mnt uts ipc user; do [ -e /proc/self/ns/$ns ] || { say err "namespace $ns missing"; fail=1; }; done
[ $fail = 0 ] && say ok "namespaces available"
grep -qw overlay /proc/filesystems || modprobe overlay 2>/dev/null; grep -qw overlay /proc/filesystems && say ok "OverlayFS" || say warn "OverlayFS unavailable (vfs/fuse fallback)"
command -v docker >/dev/null || { say err "Docker Engine not installed"; exit 1; }
docker info >/dev/null 2>&1 || { say err "Docker daemon not running/accessible"; exit 1; }
say ok "Docker daemon $(docker version -f '{{.Server.Version}}' 2>/dev/null)"
name="nexora-dockertest-$$"
if docker run --name "$name" --rm --network bridge --cap-drop ALL --read-only --memory 32m --pids-limit 32 busybox:stable sh -c 'echo ok' 2>>"$NX_LOG" | grep -q ok; then
  say ok "Test container started (net, limits, read-only rootfs)"
else say err "Test container failed (see $NX_LOG)"; fail=1; fi
docker rm -f "$name" >/dev/null 2>&1; docker image rm busybox:stable >/dev/null 2>&1
exit $fail
