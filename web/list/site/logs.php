<?php
use function Hestiacp\quoteshellarg\quoteshellarg;
$TAB = "SITES";
include $_SERVER["DOCUMENT_ROOT"] . "/inc/main.php";
$domain = $_GET["domain"] ?? "";
if (!preg_match('/^[a-z0-9.-]+$/i', $domain)) { http_response_code(400); exit(); }
exec(HESTIA_CMD . "v-nexora-site logs " . $user . " " . quoteshellarg($domain) . " 2>&1", $output, $rv);
header("Content-Type: text/plain; charset=utf-8");
header("X-Content-Type-Options: nosniff");
echo implode("\n", $output);
