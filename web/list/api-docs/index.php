<?php
$TAB = "SERVER";
include $_SERVER["DOCUMENT_ROOT"] . "/inc/main.php";
$api_url = "https://" . ($_SERVER["HTTP_HOST"] ?? "server:8083") . "/api/";
render_page($user, $TAB, "list_api_docs");
