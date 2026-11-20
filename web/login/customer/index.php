<?php
// NEXORA: reseller "log in as customer" and return. Ownership verified server side.
use function Hestiacp\quoteshellarg\quoteshellarg;
ob_start();
include $_SERVER["DOCUMENT_ROOT"] . "/inc/main.php";
verify_csrf($_GET);

if (!empty($_GET["back"])) {
	if (!empty($_SESSION["nx_reseller_origin"])) {
		$origin = $_SESSION["nx_reseller_origin"];
		session_regenerate_id(true);
		$_SESSION["user"] = $origin;
		$_SESSION["userContext"] = "user";
		$_SESSION["look"] = "";
		unset($_SESSION["nx_reseller_origin"]);
	}
	header("Location: /list/customers/");
	exit();
}

$me = $_SESSION["user"] ?? "";
$customer = $_GET["user"] ?? "";
if (
	$_SESSION["userContext"] !== "user" ||
	!empty($_SESSION["nx_reseller_origin"]) ||
	!preg_match('/^[a-z][a-z0-9_-]{1,30}$/', $customer)
) {
	http_response_code(403);
	exit("Forbidden");
}
exec(
	HESTIA_CMD . "v-nexora-reseller owns " . quoteshellarg($me) . " " . quoteshellarg($customer),
	$o,
	$rv,
);
if ($rv !== 0) {
	http_response_code(403);
	exit("Forbidden");
}
exec(
	HESTIA_CMD .
		"v-log-action " .
		quoteshellarg($me) .
		" 'Info' 'Security' " .
		quoteshellarg("Reseller logged in as customer $customer"),
);
session_regenerate_id(true);
$_SESSION["nx_reseller_origin"] = $me;
$_SESSION["user"] = $customer;
$_SESSION["userContext"] = "user";
$_SESSION["look"] = "";
header("Location: /list/web/");
exit();
