<?php
// NEXORA Mail -> Mailcow (OPSEC option, central instance)
use function Hestiacp\quoteshellarg\quoteshellarg;
ob_start();
$TAB = "MAIL";
include $_SERVER["DOCUMENT_ROOT"] . "/inc/main.php";
if ($_SESSION["userContext"] != "admin") {
	header("Location: /list/mail/");
	exit();
}
$q = fn($v) => quoteshellarg((string) $v);
$allowed = [
	"install" => ["host"],
	"connect" => ["url", "key"],
	"add-domain" => ["domain", "quota"],
	"del-domain" => ["domain"],
	"add-mailbox" => ["local", "domain", "password", "quota"],
	"del-mailbox" => ["address"],
	"passwd-mailbox" => ["address", "password"],
	"add-alias" => ["address", "goto"],
	"del-alias" => ["id"],
];
if ($_SERVER["REQUEST_METHOD"] === "POST" && $read_only !== true) {
	verify_csrf($_POST);
	$a = $_POST["action"] ?? "";
	if (isset($allowed[$a])) {
		$args = array_map(fn($f) => $q($_POST[$f] ?? ""), $allowed[$a]);
		exec(
			HESTIA_CMD . "v-nexora-mailcow " . $q($a) . " " . implode(" ", $args) . " 2>&1",
			$output,
			$rv,
		);
		check_return_code($rv, $output);
		if ($rv == 0) {
			$_SESSION["ok_msg"] = htmlentities(implode(" ", $output) ?: _("Saved."));
		}
	}
	header("Location: /list/mailcow/");
	exit();
}
$docker =
	is_readable("/etc/nexora/nexora.conf") &&
	preg_match("/^MODE=docker/m", file_get_contents("/etc/nexora/nexora.conf"));
exec(HESTIA_CMD . "v-nexora-mailcow status 2>/dev/null", $o);
$status = json_decode(implode("", $o), true);
unset($o);
$connected = !empty($status["version"]);
$data = [];
if ($connected) {
	foreach (["domains", "mailboxes", "aliases"] as $k) {
		exec(HESTIA_CMD . "v-nexora-mailcow " . $k . " 2>/dev/null", $o);
		$data[$k] = json_decode(implode("", $o), true) ?: [];
		unset($o);
	}
}
render_page($user, $TAB, "list_mailcow");
