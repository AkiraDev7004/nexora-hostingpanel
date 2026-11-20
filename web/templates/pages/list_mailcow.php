<?php $tok = tohtml($_SESSION["token"]);
$f = function ($a, $fields, $label) use ($tok) {
	echo '<form method="post" class="u-mb10 u-side-by-side"><input type="hidden" name="token" value="' . $tok . '"><input type="hidden" name="action" value="' . $a . '">' . $fields . '<button class="button">' . tohtml($label) . "</button></form>";
}; ?>
<div class="container">
	<h1 class="u-mb10">Mailcow <span class="u-text-small">— OPSEC</span></h1>
	<p class="hint u-mb20"><?= tohtml(_("Central Mailcow instance with spam filter, DKIM, SPF/DMARC and TLS. No mail system guarantees anonymity.")) ?></p>
	<?php show_alert_message($_SESSION); ?>
	<?php if (!$docker) { ?>
		<p class="alert alert-info"><?= tohtml(_("Docker is not available on this system. NEXORA is running in Classic Hosting Mode. Mailcow is not available – use Postfix/Dovecot/Roundcube (Kein OPSEC).")) ?></p>
	<?php } elseif (!$connected) { ?>
		<h2 class="u-mb10"><?= tohtml(_("1. Install central instance")) ?></h2>
		<?php $f("install", '<input class="form-control" name="host" placeholder="mail.example.com" required>', _("Install Mailcow")); ?>
		<h2 class="u-mb10 u-mt20"><?= tohtml(_("2. Connect API")) ?></h2>
		<?php $f("connect", '<input class="form-control" name="url" placeholder="https://mail.example.com" required><input class="form-control" name="key" type="password" placeholder="API key" required>', _("Connect")); ?>
	<?php } else { ?>
		<p class="u-mb20">Mailcow <?= tohtml($status["version"]) ?> · <?= tohtml(_("connected")) ?></p>
		<h2 class="u-mb10"><?= tohtml(_("Domains")) ?></h2>
		<?php $f("add-domain", '<input class="form-control" name="domain" placeholder="example.com" required><input class="form-control" name="quota" type="number" value="10240" title="MB">', _("Add")); ?>
		<div class="units-table u-mb20"><?php foreach ($data["domains"] as $d) { ?><div class="units-table-row">
			<div class="units-table-cell u-text-bold"><?= tohtml($d["domain_name"] ?? "") ?></div>
			<div class="units-table-cell"><?= tohtml(($d["mboxes_in_domain"] ?? 0) . " " . _("mailboxes")) ?></div>
			<div class="units-table-cell"><?php $f("del-domain", '<input type="hidden" name="domain" value="' . tohtml($d["domain_name"] ?? "") . '">', _("Delete")); ?></div>
		</div><?php } ?></div>
		<h2 class="u-mb10"><?= tohtml(_("Mailboxes")) ?></h2>
		<?php $f("add-mailbox", '<input class="form-control" name="local" placeholder="info" required><input class="form-control" name="domain" placeholder="example.com" required><input class="form-control" type="password" name="password" minlength="10" placeholder="Password" required><input class="form-control" name="quota" type="number" value="1024" title="MB">', _("Add")); ?>
		<div class="units-table u-mb20"><?php foreach ($data["mailboxes"] as $m) { $u = tohtml($m["username"] ?? ""); ?><div class="units-table-row">
			<div class="units-table-cell u-text-bold"><?= $u ?></div>
			<div class="units-table-cell"><?= tohtml(round(($m["quota_used"] ?? 0) / 1048576) . " / " . round(($m["quota"] ?? 0) / 1048576) . " MB") ?></div>
			<div class="units-table-cell"><?php $f("passwd-mailbox", '<input type="hidden" name="address" value="' . $u . '"><input class="form-control" type="password" name="password" minlength="10" placeholder="New password" required>', _("Change")); $f("del-mailbox", '<input type="hidden" name="address" value="' . $u . '">', _("Delete")); ?></div>
		</div><?php } ?></div>
		<h2 class="u-mb10"><?= tohtml(_("Aliases")) ?></h2>
		<?php $f("add-alias", '<input class="form-control" name="address" placeholder="sales@example.com" required><input class="form-control" name="goto" placeholder="info@example.com" required>', _("Add")); ?>
		<div class="units-table"><?php foreach ($data["aliases"] as $al) { ?><div class="units-table-row">
			<div class="units-table-cell u-text-bold"><?= tohtml($al["address"] ?? "") ?></div><div class="units-table-cell">→ <?= tohtml($al["goto"] ?? "") ?></div>
			<div class="units-table-cell"><?php $f("del-alias", '<input type="hidden" name="id" value="' . tohtml($al["id"] ?? "") . '">', _("Delete")); ?></div>
		</div><?php } ?></div>
	<?php } ?>
</div>
