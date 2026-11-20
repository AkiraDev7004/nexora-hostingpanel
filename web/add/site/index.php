<?php
// NEXORA: CloudPanel-style "Create Website" wizard
use function Hestiacp\quoteshellarg\quoteshellarg;
ob_start();
$TAB = "SITES";
include $_SERVER["DOCUMENT_ROOT"] . "/inc/main.php";

$types = ["php", "wordpress", "node", "python", "static", "proxy"];
if (!empty($_POST["ok"]) && $read_only !== true) {
	verify_csrf($_POST);
	$type = $_POST["v_type"] ?? "";
	$domain = strtolower(trim($_POST["v_domain"] ?? ""));
	if (!in_array($type, $types, true)) { $_SESSION["error_msg"] = _("Invalid application type"); }
	elseif (!preg_match('/^(?=.{1,253}$)([a-z0-9-]{1,63}\.)+[a-z]{2,63}$/', $domain)) { $_SESSION["error_msg"] = _("Invalid domain"); }
	if (empty($_SESSION["error_msg"])) {
		$args = [
			"add", $domain, $type, $_POST["v_ip"] ?? "", $_POST["v_version"] ?? "",
			$_POST["v_cpu"] ?? "1", $_POST["v_mem"] ?? "512", $_POST["v_upstream"] ?? "",
			empty($_POST["v_ssl"]) ? "no" : "yes", empty($_POST["v_dns"]) ? "no" : "yes", empty($_POST["v_mail"]) ? "no" : "yes",
		];
		$q = array_map(fn($a) => quoteshellarg((string) $a), $args);
		array_splice($q, 1, 0, [$user]);
		exec(HESTIA_CMD . "v-nexora-site " . implode(" ", $q) . " 2>&1", $output, $return_var);
		check_return_code($return_var, $output);
		if ($return_var == 0) {
			$_SESSION["ok_msg"] = htmlentities(sprintf(_("Website %s created."), $domain)) . " " . htmlentities(implode(" ", $output));
			header("Location: /list/site/"); exit();
		}
		unset($output);
	}
}
exec(HESTIA_CMD . "v-list-user-ips " . $user . " json", $output, $rv);
$ips = json_decode(implode("", $output), true) ?: [];
unset($output);
$docker = is_readable("/etc/nexora/nexora.conf") && preg_match("/^MODE=docker/m", file_get_contents("/etc/nexora/nexora.conf"));
render_page($user, $TAB, "add_site");
