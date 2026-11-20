<!doctype html>
<html class="no-js" lang="<?= $_SESSION["LANGUAGE"] ?>">

<head>
<?php
require $_SERVER["HESTIA"] . "/web/templates/includes/title.php";
require $_SERVER["HESTIA"] . "/web/templates/includes/css.php";
require $_SERVER["HESTIA"] . "/web/templates/includes/js.php";
?>
<?php if (nx_brand("favicon")) { ?><link rel="icon" href="<?= tohtml(nx_brand("favicon")) ?>"><?php } ?>
<?php if (nx_brand("accent")) { ?><style>:root{--nx-accent:<?= tohtml(nx_brand("accent")) ?>}.button:not(.button-secondary){background-color:var(--nx-accent);border-color:var(--nx-accent)}.main-menu-item-link.active{border-bottom-color:var(--nx-accent)}</style><?php } ?>
</head>

<body class="page-<?= strtolower($TAB) ?> lang-<?= $_SESSION["language"] ?>">
	<div class="app">
