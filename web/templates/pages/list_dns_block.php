<?php $tok = tohtml($_SESSION["token"]);
$f = function ($action, $fields, $label) use ($tok) {
	echo '<form method="post" class="u-mb10 u-side-by-side"><input type="hidden" name="token" value="' . $tok . '"><input type="hidden" name="action" value="' . $action . '">' . $fields . '<button class="button">' . tohtml($label) . '</button></form>';
}; ?>
<div class="container">
	<h1 class="u-mb10"><?= tohtml(_("DNS Blocking & Redirects")) ?></h1>
	<p class="hint u-mb20"><?= tohtml(_("Applies to the NEXORA recursive resolver only. Authoritative nameservers stay non-recursive. DNS overrides are not HTTP redirects (use Websites → Redirects).")) ?></p>
	<?php show_alert_message($_SESSION); ?>
	<h2 class="u-mb10"><?= tohtml(_("Block domain")) ?></h2>
	<?php $f("add-block", '<input class="form-control" name="domain" placeholder="example.com or ads.example.com" required>
		<select class="form-select" name="scope" title="Scope"><option value="all">Domain + all subdomains</option><option value="exact">Only this domain</option><option value="sub">Only subdomains (*.domain)</option></select>
		<select class="form-select" name="mode"><option value="nxdomain">NXDOMAIN</option><option value="null">0.0.0.0 / ::</option><option value="sinkhole">Sinkhole IP</option></select>
		<input class="form-control" name="v4" placeholder="Sinkhole IPv4"><input class="form-control" name="v6" placeholder="Sinkhole IPv6">', _("Block")); ?>
	<h2 class="u-mb10 u-mt20"><?= tohtml(_("DNS Redirect (override)")) ?></h2>
	<?php $f("add-redirect", '<input class="form-control" name="domain" placeholder="example.com" required>
		<select class="form-select" name="scope" title="Scope"><option value="all">Domain + all subdomains</option><option value="exact">Only this domain</option><option value="sub">Only subdomains (*.domain)</option></select>
		<select class="form-select" name="rtype"><option>A</option><option>AAAA</option><option>CNAME</option></select>
		<input class="form-control" name="target" placeholder="192.0.2.50" required><input class="form-control" name="ttl" value="300" type="number" min="0">', _("Add")); ?>
	<div class="units-table u-mt20">
		<div class="units-table-header"><div class="units-table-cell">Type</div><div class="units-table-cell">Domain</div><div class="units-table-cell">Answer</div><div class="units-table-cell"></div></div>
		<?php foreach ($rules as $r) { ?>
		<div class="units-table-row">
			<div class="units-table-cell"><?= tohtml($r["TYPE"]) ?></div>
			<div class="units-table-cell u-text-bold"><?= tohtml($r["DOMAIN"]) ?></div>
			<div class="units-table-cell"><?= tohtml($r["MODE"] . " " . $r["VALUE"] . " " . $r["TTL"]) ?> · <?= tohtml(["all" => _("domain + subdomains"), "exact" => _("domain only"), "sub" => _("subdomains only")][$r["SCOPE"]] ?? $r["SCOPE"]) ?></div>
			<div class="units-table-cell"><?php $f($r["TYPE"] === "BLOCK" ? "del-block" : "del-redirect", '<input type="hidden" name="domain" value="' . tohtml($r["DOMAIN"]) . '">', _("Delete")); ?></div>
		</div>
		<?php } ?>
	</div>
	<h2 class="u-mb10 u-mt20"><?= tohtml(_("Blocklists")) ?></h2>
	<?php $f("add-list", '<input class="form-control" name="name" placeholder="name" pattern="[A-Za-z0-9_-]+" required><input class="form-control" name="url" placeholder="https://..." required>', _("Import")); ?>
	<div class="units-table">
		<?php foreach ($lists as $l) { ?>
		<div class="units-table-row">
			<div class="units-table-cell u-text-bold"><?= tohtml($l["NAME"]) ?></div>
			<div class="units-table-cell"><?= tohtml($l["URL"]) ?></div>
			<div class="units-table-cell"><?= tohtml($l["COUNT"]) ?> domains · <?= tohtml($l["ENABLED"] === "yes" ? _("enabled") : _("disabled")) ?> · <?= tohtml($l["UPDATED"]) ?></div>
			<div class="units-table-cell"><?php foreach (["update-list" => _("Update"), "toggle-list" => _("Enable/Disable"), "del-list" => _("Delete")] as $a => $lbl) { $f($a, '<input type="hidden" name="name" value="' . tohtml($l["NAME"]) . '">', $lbl); } ?></div>
		</div>
		<?php } ?>
	</div>
	<h2 class="u-mb10 u-mt20"><?= tohtml(_("Resolver")) ?></h2>
	<?php $f("resolver", '<input class="form-control" name="listen" placeholder="10.0.0.1@53" required><input class="form-control" name="allow" placeholder="10.0.0.0/24,127.0.0.0/8" required>', _("Apply")); ?>
</div>
