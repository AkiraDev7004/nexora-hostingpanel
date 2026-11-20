<footer class="app-footer">
	<div class="container">
		<p>
			<?= tohtml(nx_brand("footer", ($_SESSION["APP_NAME"] ?? "NEXORA Panel") . " v" . $_SESSION["VERSION"])) ?>
			<?php if (nx_brand("support_url")) { ?>&middot; <a class="app-footer-link" href="<?= tohtml(nx_brand("support_url")) ?>" rel="noopener" target="_blank">Support</a><?php } ?>
			<?php if (nx_brand("docs_url")) { ?>&middot; <a class="app-footer-link" href="<?= tohtml(nx_brand("docs_url")) ?>" rel="noopener" target="_blank">Docs</a><?php } ?>
			&middot; <a href="https://github.com/hestiacp/hestiacp" class="app-footer-link" target="_blank" rel="noopener">Based on HestiaCP (GPL-3.0)</a>
		</p>
	</div>
</footer>
