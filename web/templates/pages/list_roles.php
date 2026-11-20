<div class="container">
	<h1 class="u-mb10"><?= tohtml(_("Roles")) ?></h1>
	<p class="hint u-mb20">Super Admin (<?= tohtml($_SESSION["ROOT_USER"] ?? "admin") ?>) · Admin · Reseller · User</p>
	<?php show_alert_message($_SESSION); ?>
	<div class="units-table">
		<?php foreach ($users as $u => $d) { ?>
		<div class="units-table-row">
			<div class="units-table-cell u-text-bold"><?= tohtml($u) ?></div>
			<div class="units-table-cell"><?= tohtml($roles[$u]) ?></div>
			<div class="units-table-cell">
				<form method="post" class="u-side-by-side"><input type="hidden" name="token" value="<?= tohtml($_SESSION["token"]) ?>"><input type="hidden" name="user" value="<?= tohtml($u) ?>">
					<select class="form-select" name="role"><?php foreach (["user", "reseller", "admin"] as $r) { ?><option <?= $roles[$u] === $r ? "selected" : "" ?>><?= $r ?></option><?php } ?></select>
					<input class="form-control" name="packages" placeholder="allowed packages (reseller): default,basic">
					<button class="button button-secondary"><?= tohtml(_("Save")) ?></button></form>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
