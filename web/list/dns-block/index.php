<?php
// NEXORA: DNS Domain Blocking, DNS Redirects, Blocklists (resolver only, admin only)
use function Hestiacp\quoteshellarg\quoteshellarg;
ob_start();
$TAB = "DNS";
include $_SERVER["DOCUMENT_ROOT"] . "/inc/main.php";
if ($_SESSION["userContext"] != "admin") { header("Location: /list/user/"); exit(); }

$q = fn($v) => quoteshellarg((string) $v);
if ($_SERVER["REQUEST_METHOD"] === "POST" && $read_only !== true) {
	verify_csrf($_POST);
	$a = $_POST["action"] ?? "";
	$map = [
		"add-block" => [$_POST["domain"] ?? "", $_POST["mode"] ?? "nxdomain", $_POST["v4"] ?? "", $_POST["v6"] ?? ""],
		"del-block" => [$_POST["domain"] ?? ""],
		"add-redirect" => [$_POST["domain"] ?? "", $_POST["rtype"] ?? "A", $_POST["target"] ?? "", $_POST["ttl"] ?? "300"],
		"del-redirect" => [$_POST["domain"] ?? ""],
		"add-list" => [$_POST["name"] ?? "", $_POST["url"] ?? ""],
		"update-list" => [$_POST["name"] ?? ""],
		"toggle-list" => [$_POST["name"] ?? ""],
		"del-list" => [$_POST["name"] ?? ""],
		"resolver" => [$_POST["listen"] ?? "", $_POST["allow"] ?? ""],
	];
	if (isset($map[$a])) {
		exec(HESTIA_CMD . "v-nexora-dns-block " . $q($a) . " " . implode(" ", array_map($q, $map[$a])) . " 2>&1", $output, $rv);
		check_return_code($rv, $output);
		if ($rv == 0) { $_SESSION["ok_msg"] = _("Saved."); }
		unset($output);
	}
	header("Location: /list/dns-block/"); exit();
}
exec(HESTIA_CMD . "v-nexora-dns-block list json", $o1); $rules = json_decode(implode("", $o1), true) ?: [];
exec(HESTIA_CMD . "v-nexora-dns-block lists json", $o2); $lists = json_decode(implode("", $o2), true) ?: [];
render_page($user, $TAB, "list_dns_block");
