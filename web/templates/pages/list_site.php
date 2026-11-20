<div class="toolbar">
	<div class="toolbar-inner">
		<div class="toolbar-buttons">
			<a href="/add/site/" class="button button-secondary js-button-create"><i class="fas fa-circle-plus icon-green"></i><?= tohtml(_("Create Website")) ?></a>
		</div>
	</div>
</div>
<div class="container">
	<?php if ($nx_mode !== "docker") { ?>
		<p class="alert alert-info u-mb20"><?= tohtml(_("Docker is not available on this system. NEXORA is running in Classic Hosting Mode.")) ?></p>
	<?php } ?>
	<?php show_alert_message($_SESSION); ?>
	<h1 class="u-mb20"><?= tohtml(_("Websites")) ?></h1>
	<div class="units-table">
		<div class="units-table-header">
			<div class="units-table-cell"><?= tohtml(_("Domain")) ?></div>
			<div class="units-table-cell"><?= tohtml(_("Type")) ?></div>
			<div class="units-table-cell"><?= tohtml(_("Mode")) ?></div>
			<div class="units-table-cell">IP</div>
			<div class="units-table-cell"><?= tohtml(_("Status")) ?></div>
			<div class="units-table-cell"><?= tohtml(_("Usage")) ?></div>
			<div class="units-table-cell"><?= tohtml(_("Limits")) ?></div>
			<div class="units-table-cell"></div>
		</div>
		<?php foreach ($data as $d => $s) { ?>
		<div class="units-table-row">
			<div class="units-table-cell u-text-bold"><a href="/edit/web/?domain=<?= urlencode($d) ?>&token=<?= tohtml($_SESSION["token"]) ?>"><?= tohtml($d) ?></a></div>
			<div class="units-table-cell"><?= tohtml($s["TYPE"] . " " . $s["VERSION"]) ?></div>
			<div class="units-table-cell"><?= tohtml($s["MODE"]) ?></div>
			<div class="units-table-cell"><?= tohtml($s["IP"]) ?></div>
			<div class="units-table-cell"><?= tohtml($s["STATE"]) ?></div>
			<div class="units-table-cell"><?= tohtml(str_replace("|", " / ", $s["USAGE"])) ?></div>
			<div class="units-table-cell"><?= tohtml($s["CPU"] . " CPU / " . $s["MEM"] . " MB") ?></div>
			<div class="units-table-cell">
				<?php foreach (["start", "stop", "restart"] + ($s["MODE"] === "docker" ? [3 => "rebuild"] : []) + [4 => "delete"] as $a) { ?>
				<form method="post" action="/nexora/site/" style="display:inline" <?= $a === "delete" ? 'onsubmit="return confirm(\'' . tohtml(_("Delete website and its data?")) . '\')"' : "" ?>>
					<input type="hidden" name="token" value="<?= tohtml($_SESSION["token"]) ?>">
					<input type="hidden" name="domain" value="<?= tohtml($d) ?>">
					<button class="button button-secondary" name="action" value="<?= $a ?>"><?= tohtml(_(ucfirst($a))) ?></button>
				</form>
				<?php } ?>
				<a class="button button-secondary" href="/list/site/logs.php?domain=<?= urlencode($d) ?>"><?= tohtml(_("Logs")) ?></a>
			</div>
		</div>
		<?php } ?>
		<?php if (!$data) { ?><p class="u-mt20"><?= tohtml(_("No websites yet.")) ?></p><?php } ?>
	</div>
</div>
