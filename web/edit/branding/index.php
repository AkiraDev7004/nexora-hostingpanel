<?php
// NEXORA Settings -> Branding (admin)
use function Hestiacp\quoteshellarg\quoteshellarg;
ob_start();
$TAB = "SERVER";
include $_SERVER["DOCUMENT_ROOT"] . "/inc/main.php";
if ($_SESSION["userContext"] != "admin") { header("Location: /list/user/"); exit(); }
$keys = ["name" => _("Product name"), "title" => _("Browser title"), "description" => _("Description"), "footer" => _("Footer"),
	"login_text" => _("Login page text"), "mail_from" => _("E-mail sender name"), "accent" => _("Accent color"),
	"support_url" => _("Support URL"), "docs_url" => _("Documentation URL")];
if ($_SERVER["REQUEST_METHOD"] === "POST" && $read_only !== true) {
	verify_csrf($_POST);
	$errs = [];
	foreach ($keys as $k => $l) {
		if (!isset($_POST[$k]) || $_POST[$k] === nx_brand($k)) { continue; }
		exec(HESTIA_CMD . "v-nexora-branding set " . $k . " " . quoteshellarg($_POST[$k]) . " 2>&1", $o, $rv);
		if ($rv) { $errs[] = "$l: " . implode(" ", $o); }
		unset($o);
	}
	if (isset($_POST["title"]) && $_POST["title"] !== "") {
		exec(HESTIA_CMD . "v-change-sys-config-value TITLE " . quoteshellarg($_POST["title"]));
	}
	if (isset($_POST["mail_from"]) && $_POST["mail_from"] !== "") {
		exec(HESTIA_CMD . "v-change-sys-config-value FROM_NAME " . quoteshellarg($_POST["mail_from"]));
	}
	foreach (["logo", "login_logo", "favicon"] as $kind) {
		if (!empty($_POST["reset_" . $kind])) {
			exec(HESTIA_CMD . "v-nexora-branding reset-image " . $kind);
		} elseif (!empty($_FILES[$kind]["tmp_name"]) && is_uploaded_file($_FILES[$kind]["tmp_name"])) {
			$tmp = tempnam(sys_get_temp_dir(), "nxb");
			move_uploaded_file($_FILES[$kind]["tmp_name"], $tmp);
			chmod($tmp, 0644);
			exec(HESTIA_CMD . "v-nexora-branding image " . $kind . " " . quoteshellarg($tmp) . " 2>&1", $o, $rv);
			if ($rv) { $errs[] = "$kind: " . implode(" ", $o); }
			unset($o);
			@unlink($tmp);
		}
	}
	load_hestia_config();
	if ($errs) { $_SESSION["error_msg"] = htmlentities(implode("; ", $errs)); } else { $_SESSION["ok_msg"] = _("Changes have been saved."); }
	header("Location: /edit/branding/"); exit();
}
render_page($user, $TAB, "edit_branding");
