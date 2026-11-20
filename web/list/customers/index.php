<?php
// NEXORA: reseller customer management (reseller sees only own customers; checked server side)
use function Hestiacp\quoteshellarg\quoteshellarg;
ob_start();
$TAB = "USER";
include $_SERVER["DOCUMENT_ROOT"] . "/inc/main.php";
$me = $_SESSION["user"];
exec(HESTIA_CMD . "v-nexora-reseller role " . quoteshellarg($me), $o);
$role = trim(implode("", $o));
unset($o);
if ($role !== "reseller") {
	header("Location: /list/user/");
	exit();
}
$q = fn($v) => quoteshellarg((string) $v);
if ($_SERVER["REQUEST_METHOD"] === "POST" && $read_only !== true) {
	verify_csrf($_POST);
	$a = $_POST["action"] ?? "";
	if ($a === "add-customer") {
		$cmd =
			"v-nexora-reseller add-customer " .
			$q($me) .
			" " .
			implode(
				" ",
				array_map(fn($k) => $q($_POST[$k] ?? ""), [
					"user",
					"password",
					"email",
					"package",
					"name",
				]),
			);
	} elseif (in_array($a, ["suspend", "unsuspend", "delete"], true)) {
		$cmd = "v-nexora-reseller " . $a . " " . $q($me) . " " . $q($_POST["customer"] ?? "");
	}
	if (!empty($cmd)) {
		exec(HESTIA_CMD . $cmd . " 2>&1", $output, $rv);
		check_return_code($rv, $output);
		if ($rv == 0) {
			$_SESSION["ok_msg"] = _("Saved.");
		}
	}
	header("Location: /list/customers/");
	exit();
}
exec(HESTIA_CMD . "v-nexora-reseller customers " . $q($me) . " json", $o);
$customers = json_decode(implode("", $o), true) ?: [];
unset($o);
exec(HESTIA_CMD . "v-list-user-packages json", $o);
$all = json_decode(implode("", $o), true) ?: [];
unset($o);
render_page($user, $TAB, "list_customers");
