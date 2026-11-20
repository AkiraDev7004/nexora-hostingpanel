<?php
// NEXORA Administration -> Roles (admin)
use function Hestiacp\quoteshellarg\quoteshellarg;
ob_start();
$TAB = "USER";
include $_SERVER["DOCUMENT_ROOT"] . "/inc/main.php";
if ($_SESSION["userContext"] != "admin") {
	header("Location: /list/user/");
	exit();
}
if ($_SERVER["REQUEST_METHOD"] === "POST" && $read_only !== true) {
	verify_csrf($_POST);
	exec(
		HESTIA_CMD .
			"v-nexora-reseller set-role " .
			quoteshellarg($_POST["user"] ?? "") .
			" " .
			quoteshellarg($_POST["role"] ?? "") .
			" " .
			quoteshellarg($_POST["packages"] ?? "") .
			" 2>&1",
		$output,
		$rv,
	);
	check_return_code($rv, $output);
	if ($rv == 0) {
		$_SESSION["ok_msg"] = _("Saved.");
	}
	header("Location: /list/roles/");
	exit();
}
exec(HESTIA_CMD . "v-list-users json", $o);
$users = json_decode(implode("", $o), true) ?: [];
unset($o);
$roles = [];
foreach (array_keys($users) as $u) {
	exec(HESTIA_CMD . "v-nexora-reseller role " . quoteshellarg($u), $o);
	$roles[$u] = trim(implode("", $o));
	unset($o);
}
render_page($user, $TAB, "list_roles");
