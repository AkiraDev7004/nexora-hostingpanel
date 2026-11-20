<div class="toolbar">
	<div class="toolbar-inner">
		<div class="toolbar-buttons">
			<a class="button button-secondary button-back js-button-back" href="/list/site/"><i class="fas fa-arrow-left icon-blue"></i><?= tohtml(_("Back")) ?></a>
		</div>
		<div class="toolbar-buttons">
			<button type="submit" class="button" form="main-form"><i class="fas fa-rocket icon-purple"></i><?= tohtml(_("Create")) ?></button>
		</div>
	</div>
</div>
<div class="container">
<form id="main-form" method="post">
	<input type="hidden" name="token" value="<?= tohtml($_SESSION["token"]) ?>">
	<input type="hidden" name="ok" value="1">
	<div class="form-container">
		<h1 class="u-mb20"><?= tohtml(_("Create Website")) ?></h1>
		<?php show_alert_message($_SESSION); ?>
		<?php if (!$docker) { ?><p class="alert alert-info u-mb20"><?= tohtml(_("Docker is not available on this system. NEXORA is running in Classic Hosting Mode.")) ?></p><?php } ?>
		<div class="u-mb20">
			<label class="form-label" for="v_type">1. <?= tohtml(_("Application")) ?></label>
			<select class="form-select" name="v_type" id="v_type">
				<?php foreach (["php" => "PHP", "wordpress" => "WordPress", "node" => "Node.js", "python" => "Python", "static" => "Static HTML/CSS/JS", "proxy" => "Reverse Proxy"] as $k => $v) { ?>
				<option value="<?= $k ?>"><?= $v ?></option>
				<?php } ?>
			</select>
		</div>
		<div class="u-mb20"><label class="form-label" for="v_domain">2. <?= tohtml(_("Domain")) ?></label>
			<input class="form-control" name="v_domain" id="v_domain" required pattern="[A-Za-z0-9.-]+"></div>
		<div class="u-mb20"><label class="form-label" for="v_ip">3. <?= tohtml(_("IP Address")) ?></label>
			<select class="form-select" name="v_ip" id="v_ip">
				<?php foreach ($ips as $ip => $i) { ?><option value="<?= tohtml($ip) ?>"><?= tohtml($ip . (empty($i["NAT"]) ? "" : " → " . $i["NAT"])) ?></option><?php } ?>
			</select></div>
		<div class="u-mb20" data-for="php wordpress node python"><label class="form-label" for="v_version">4. <?= tohtml(_("Runtime version")) ?></label>
			<select class="form-select" name="v_version" id="v_version"></select></div>
		<div class="u-mb20" data-for="proxy"><label class="form-label" for="v_upstream">4. <?= tohtml(_("Upstream URL")) ?></label>
			<input class="form-control" name="v_upstream" id="v_upstream" placeholder="http://127.0.0.1:8080"></div>
		<div class="u-mb20 u-side-by-side" data-for="php wordpress node python static">
			<div><label class="form-label" for="v_cpu">5. CPU</label><input class="form-control" type="number" step="0.25" min="0.25" name="v_cpu" id="v_cpu" value="1"></div>
			<div><label class="form-label" for="v_mem">RAM (MB)</label><input class="form-control" type="number" min="64" name="v_mem" id="v_mem" value="512"></div>
		</div>
		<div class="u-mb10"><label class="form-check"><input class="form-check-input" type="checkbox" name="v_ssl" value="1" checked> 7. Let's Encrypt SSL</label></div>
		<div class="u-mb10"><label class="form-check"><input class="form-check-input" type="checkbox" name="v_mail" value="1"> 8. <?= tohtml(_("Mail domain")) ?></label></div>
		<div class="u-mb20"><label class="form-check"><input class="form-check-input" type="checkbox" name="v_dns" value="1"> 9. <?= tohtml(_("Create DNS zone")) ?></label></div>
		<p class="hint"><?= tohtml(_("WordPress creates its database automatically. For other types add databases under Databases.")) ?></p>
	</div>
</form>
</div>
<script>
(() => {
	const v = {php: ["8.3","8.2","8.4","8.1"], wordpress: ["8.3","8.2","8.4"], node: ["22","20","24"], python: ["3.12","3.13","3.11"]};
	const t = document.getElementById("v_type"), s = document.getElementById("v_version");
	const upd = () => {
		document.querySelectorAll("[data-for]").forEach(e => e.hidden = !e.dataset.for.split(" ").includes(t.value));
		s.innerHTML = ""; (v[t.value] || []).forEach(x => s.add(new Option(x, x)));
	};
	t.addEventListener("change", upd); upd();
})();
</script>
