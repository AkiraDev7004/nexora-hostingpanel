<div class="toolbar"><div class="toolbar-inner"><div class="toolbar-buttons">
	<?php foreach (["containers" => _("Containers"), "images" => _("Images"), "networks" => _("Networks"), "usage" => _("Resource Usage")] as $k => $l) { ?>
	<a class="button <?= $dtab === $k ? "" : "button-secondary" ?>" href="/list/docker/?tab=<?= $k ?>"><?= tohtml($l) ?></a>
	<?php } ?>
</div></div></div>
<div class="container">
	<h1 class="u-mb20">Docker</h1>
	<?php show_alert_message($_SESSION); ?>
	<?php if (!$docker_ok) { ?>
		<p class="alert alert-info"><?= tohtml($docker_error) ?></p>
	<?php } elseif (!$rows) { ?>
		<p><?= tohtml(_("Nothing to show.")) ?></p>
	<?php } else { ?>
		<?php if ($dtab === "images") { ?>
		<form method="post" class="u-mb10"><input type="hidden" name="token" value="<?= tohtml($_SESSION["token"]) ?>"><input type="hidden" name="tab" value="images">
			<button class="button button-secondary" name="action" value="image-prune"><?= tohtml(_("Remove unused images")) ?></button></form>
		<?php } ?>
		<div class="units-table">
			<div class="units-table-header">
				<?php foreach (array_keys($rows[0]) as $h) { ?><div class="units-table-cell"><?= tohtml($h) ?></div><?php } ?>
				<?php if ($dtab === "images") { ?><div class="units-table-cell"></div><?php } ?>
			</div>
			<?php foreach ($rows as $r) { ?>
			<div class="units-table-row">
				<?php foreach ($r as $v) { ?><div class="units-table-cell"><?= tohtml($v) ?></div><?php } ?>
				<?php if ($dtab === "images") { ?><div class="units-table-cell">
					<form method="post"><input type="hidden" name="token" value="<?= tohtml($_SESSION["token"]) ?>"><input type="hidden" name="tab" value="images"><input type="hidden" name="id" value="<?= tohtml($r["ID"]) ?>">
					<button class="button button-secondary" name="action" value="image-rm"><?= tohtml(_("Delete")) ?></button></form></div><?php } ?>
			</div>
			<?php } ?>
		</div>
		<?php if ($dtab === "containers") { ?><p class="hint u-mt20"><?= tohtml(_("Start, stop, rebuild and limits are managed per website under Websites.")) ?></p><?php } ?>
	<?php } ?>
</div>
