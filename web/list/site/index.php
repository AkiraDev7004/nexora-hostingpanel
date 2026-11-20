<?php
// NEXORA: per-website runtime list (containers / classic services)
$TAB = "SITES";
include $_SERVER["DOCUMENT_ROOT"] . "/inc/main.php";
exec(HESTIA_CMD . "v-nexora-site list " . $user . " json", $output, $return_var);
$data = json_decode(implode("", $output), true) ?: [];
unset($output);
$nx_mode = "classic";
if (is_readable("/etc/nexora/nexora.conf") && preg_match("/^MODE=(\w+)/m", file_get_contents("/etc/nexora/nexora.conf"), $m)) {
	$nx_mode = $m[1];
}
render_page($user, $TAB, "list_site");
$_SESSION["back"] = $_SERVER["REQUEST_URI"];
