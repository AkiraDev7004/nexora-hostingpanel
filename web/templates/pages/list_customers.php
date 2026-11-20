<?php $tok = tohtml($_SESSION["token"]); ?>
<div class="container">
	<h1 class="u-mb20"><?= tohtml(_("My Customers")) ?></h1>
	<?php show_alert_message($_SESSION); ?>
	<form method="post" class="u-mb20 u-side-by-side">
		<input type="hidden" name="token" value="<?= $tok ?>"><input type="hidden" name="action" value="add-customer">
		<input class="form-control" name="user" placeholder="username" pattern="[a-z][a-z0-9_-]+" required>
		<input class="form-control" name="name" placeholder="Name">
		<input class="form-control" name="email" type="email" placeholder="E-mail" required>
		<input class="form-control" name="password" type="password" minlength="10" placeholder="Password" required>
		<select class="form-select" name="package"><?php foreach (array_keys($all) as $p) { ?><option><?= tohtml($p) ?></option><?php } ?></select>
		<button class="button"><?= tohtml(_("Create customer")) ?></button>
	</form>
	<div class="units-table">
		<?php foreach ($customers as $u => $c) { ?>
		<div class="units-table-row">
			<div class="units-table-cell u-text-bold"><?= tohtml($u) ?></div>
			<div class="units-table-cell"><?= tohtml($c["NAME"] . " · " . $c["EMAIL"]) ?></div>
			<div class="units-table-cell"><?= tohtml($c["PACKAGE"]) ?></div>
			<div class="units-table-cell"><?= tohtml($c["WEB"] . " " . _("web domains") . " · " . $c["DISK"] . " MB") ?></div>
			<div class="units-table-cell">
				<?php foreach ([$c["SUSPENDED"] === "yes" ? "unsuspend" : "suspend", "delete"] as $a) { ?>
				<form method="post" style="display:inline" <?= $a === "delete" ? 'onsubmit="return confirm(\'Delete?\')"' : "" ?>><input type="hidden" name="token" value="<?= $tok ?>"><input type="hidden" name="customer" value="<?= tohtml($u) ?>"><button class="button button-secondary" name="action" value="<?= $a ?>"><?= tohtml(_(ucfirst($a))) ?></button></form>
				<?php } ?>
			</div>
		</div>
		<?php } ?>
		<?php if (!$customers) { ?><p><?= tohtml(_("No customers yet.")) ?></p><?php } ?>
	</div>
	<p class="hint u-mt20"><?= tohtml(_("Only packages allowed by the administrator can be assigned.")) ?></p>
</div>
