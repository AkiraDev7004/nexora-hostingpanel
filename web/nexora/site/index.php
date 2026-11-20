<?php
// NEXORA: start/stop/restart/rebuild/delete/limits for one website (POST + CSRF)
use function Hestiacp\quoteshellarg\quoteshellarg;
ob_start();
include $_SERVER["DOCUMENT_ROOT"] . "/inc/main.php";
verify_csrf($_POST);
if ($read_only === true) { header("Location: /list/site/"); exit(); }
$allowed = ["start", "stop", "restart", "rebuild", "delete", "limits"];
$action = $_POST["action"] ?? "";
$domain = $_POST["domain"] ?? "";
if (!in_array($action, $allowed, true) || !preg_match('/^[a-z0-9.-]+$/i', $domain)) {
	http_response_code(400); exit("Bad request");
}
$cmd = HESTIA_CMD . "v-nexora-site " . quoteshellarg($action) . " " . $user . " " . quoteshellarg($domain);
if ($action === "limits") {
	$cmd .= " " . quoteshellarg((string) (float) $_POST["cpu"]) . " " . quoteshellarg((string) (int) $_POST["mem"]);
}
exec($cmd . " 2>&1", $output, $return_var);
check_return_code($return_var, $output);
if ($return_var == 0) { $_SESSION["ok_msg"] = htmlentities("$domain: $action OK"); }
header("Location: /list/site/");
exit();
