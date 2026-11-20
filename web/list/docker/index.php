<?php
// NEXORA Docker Management: Containers, Images, Networks, Resource Usage (admin)
use function Hestiacp\quoteshellarg\quoteshellarg;
ob_start();
$TAB = "DOCKER";
include $_SERVER["DOCUMENT_ROOT"] . "/inc/main.php";
if ($_SESSION["userContext"] != "admin") { header("Location: /list/site/"); exit(); }
if ($_SERVER["REQUEST_METHOD"] === "POST" && $read_only !== true) {
	verify_csrf($_POST);
	$a = $_POST["action"] ?? "";
	if ($a === "image-rm") {
		exec(HESTIA_CMD . "v-nexora-docker image-rm " . quoteshellarg($_POST["id"] ?? "") . " 2>&1", $output, $rv);
	} elseif ($a === "image-prune") {
		exec(HESTIA_CMD . "v-nexora-docker image-prune 2>&1", $output, $rv);
	}
	if (isset($rv)) { check_return_code($rv, $output); }
	header("Location: /list/docker/?tab=" . urlencode($_POST["tab"] ?? "containers")); exit();
}
$dtab = in_array($_GET["tab"] ?? "", ["containers", "images", "networks", "usage"], true) ? $_GET["tab"] : "containers";
exec(HESTIA_CMD . "v-nexora-docker " . $dtab . " 2>&1", $output, $rv);
$docker_ok = $rv == 0;
$rows = $docker_ok ? (json_decode(implode("", $output), true) ?: []) : [];
$docker_error = $docker_ok ? "" : implode(" ", $output);
unset($output);
render_page($user, $TAB, "list_docker");
