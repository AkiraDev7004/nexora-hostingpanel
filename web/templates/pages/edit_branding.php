<div class="toolbar"><div class="toolbar-inner">
	<div class="toolbar-buttons"><a class="button button-secondary button-back" href="/edit/server/"><i class="fas fa-arrow-left icon-blue"></i><?= tohtml(_("Back")) ?></a></div>
	<div class="toolbar-buttons"><button type="submit" class="button" form="main-form"><i class="fas fa-floppy-disk icon-purple"></i><?= tohtml(_("Save")) ?></button></div>
</div></div>
<div class="container">
<form id="main-form" method="post" enctype="multipart/form-data">
	<input type="hidden" name="token" value="<?= tohtml($_SESSION["token"]) ?>">
	<div class="form-container">
		<h1 class="u-mb20"><?= tohtml(_("Branding")) ?></h1>
		<?php show_alert_message($_SESSION); ?>
		<?php foreach ($keys as $k => $l) { ?>
		<div class="u-mb10"><label class="form-label" for="b_<?= $k ?>"><?= tohtml($l) ?></label>
			<input class="form-control" id="b_<?= $k ?>" name="<?= $k ?>" <?= $k === "accent" ? 'type="color"' : "" ?> value="<?= tohtml(nx_brand($k, $k === "accent" ? "#ffffff" : "")) ?>"></div>
		<?php } ?>
		<?php foreach (["logo" => _("Logo"), "login_logo" => _("Login logo"), "favicon" => _("Favicon")] as $k => $l) { ?>
		<div class="u-mb10"><label class="form-label" for="f_<?= $k ?>"><?= tohtml($l) ?> (PNG/JPEG/WEBP, max 1 MB)</label>
			<?php if (nx_brand($k)) { ?><img src="<?= tohtml(nx_brand($k)) ?>" alt="" height="32" class="u-mb10"><?php } ?>
			<input class="form-control" type="file" id="f_<?= $k ?>" name="<?= $k ?>" accept="image/png,image/jpeg,image/webp">
			<label class="form-check"><input class="form-check-input" type="checkbox" name="reset_<?= $k ?>" value="1"> <?= tohtml(_("Reset to default")) ?></label></div>
		<?php } ?>
		<p class="hint u-mt20"><?= tohtml(_("License notices (GPL-3.0, HestiaCP origin) remain visible as legally required.")) ?></p>
	</div>
</form>
</div>
