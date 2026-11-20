<div class="container nx-docs">
<style>.nx-docs pre{background:var(--color-background);border:1px solid #202020;padding:12px;overflow:auto;margin:8px 0 20px;font-size:13px}.nx-docs table{width:100%;border-collapse:collapse;margin-bottom:24px}.nx-docs td,.nx-docs th{border:1px solid #202020;padding:6px 8px;text-align:left;vertical-align:top;font-size:14px}.nx-docs h2{margin:28px 0 8px}</style>
<h1>API Documentation</h1>
<h2>API URL</h2><pre><?= tohtml($api_url) ?></pre>
<h2>Authentication</h2>
<p>Create a key under <a href="/list/access-key/">Settings → API Management</a>. The secret is shown once and stored only as a SHA-256 hash. Keys can expire, be disabled and are bound to permission groups (e.g. <code>nexora-websites</code>, <code>nexora-admin</code>). Each request is a <code>POST</code> with form fields:</p>
<table><tr><th>Field</th><th>Description</th></tr>
<tr><td><code>access_key</code></td><td>Access key ID</td></tr><tr><td><code>secret_key</code></td><td>Secret</td></tr>
<tr><td><code>cmd</code></td><td>Command name</td></tr><tr><td><code>arg1</code> … <code>arg9</code></td><td>Positional arguments</td></tr>
<tr><td><code>returncode</code></td><td><code>yes</code> = only return exit code, <code>no</code> = return output</td></tr></table>
<h2>Endpoints</h2>
<table><tr><th>Area</th><th>cmd</th><th>Arguments</th><th>Description</th></tr>
<tr><td>Websites</td><td><code>v-nexora-site</code></td><td><code>add USER DOMAIN TYPE IP [VERSION] [CPU] [MEM_MB] [UPSTREAM] [SSL yes|no] [DNS yes|no] [MAIL yes|no]</code></td><td>Create website. TYPE: php, wordpress, node, python, static, proxy.</td></tr>
<tr><td>Websites</td><td><code>v-nexora-site</code></td><td><code>list USER json</code></td><td>List websites with mode, state, usage.</td></tr>
<tr><td>Websites</td><td><code>v-nexora-site</code></td><td><code>start|stop|restart|rebuild|delete|status|logs|migrate USER DOMAIN</code></td><td>Lifecycle of one website.</td></tr>
<tr><td>Websites</td><td><code>v-nexora-site</code></td><td><code>limits USER DOMAIN CPU MEM_MB</code></td><td>Change resource limits.</td></tr>
<tr><td>Docker</td><td><code>v-nexora-docker</code></td><td><code>containers|images|networks|usage json</code></td><td>Docker inventory and live usage (admin).</td></tr>
<tr><td>Docker</td><td><code>v-nexora-docker</code></td><td><code>image-rm IMAGE_ID | image-prune</code></td><td>Image management (admin).</td></tr>
<tr><td>Databases</td><td><code>v-add-database</code></td><td><code>USER DATABASE DBUSER DBPASS [TYPE mysql|pgsql]</code></td><td>Create database.</td></tr>
<tr><td>Databases</td><td><code>v-list-databases</code></td><td><code>USER json</code></td><td>List databases.</td></tr>
<tr><td>Domains</td><td><code>v-add-web-domain-alias</code></td><td><code>USER DOMAIN ALIAS</code></td><td>Add domain alias.</td></tr>
<tr><td>Domains</td><td><code>v-add-web-domain-redirect</code></td><td><code>USER DOMAIN URL CODE(301|302)</code></td><td>HTTP redirect via reverse proxy.</td></tr>
<tr><td>SSL</td><td><code>v-add-letsencrypt-domain</code></td><td><code>USER DOMAIN [ALIASES]</code></td><td>Issue Let's Encrypt certificate.</td></tr>
<tr><td>SSL</td><td><code>v-list-web-domain-ssl</code></td><td><code>USER DOMAIN json</code></td><td>Certificate details and expiry.</td></tr>
<tr><td>Mail</td><td><code>v-add-mail-account</code></td><td><code>USER DOMAIN ACCOUNT PASSWORD [QUOTA]</code></td><td>Postfix/Dovecot mailbox (Kein OPSEC).</td></tr>
<tr><td>Mail</td><td><code>v-nexora-mailcow</code></td><td><code>domains|mailboxes|aliases | add-mailbox LOCAL DOMAIN PASSWORD [QUOTA]</code></td><td>Mailcow (OPSEC), admin.</td></tr>
<tr><td>DNS</td><td><code>v-add-dns-domain</code></td><td><code>USER DOMAIN IP</code></td><td>Create authoritative zone.</td></tr>
<tr><td>DNS</td><td><code>v-add-dns-record</code></td><td><code>USER DOMAIN RECORD TYPE VALUE [PRIORITY] [ID] [RESTART] [TTL]</code></td><td>Add record (A, AAAA, CNAME, MX, TXT, NS, SRV, CAA, PTR).</td></tr>
<tr><td>DNS</td><td><code>v-nexora-dns-block</code></td><td><code>add-block DOMAIN [nxdomain|null|sinkhole] [V4] [V6] | add-redirect DOMAIN A|AAAA|CNAME TARGET [TTL] | add-list NAME URL | list json</code></td><td>Resolver blocking and overrides (admin).</td></tr>
<tr><td>IP addresses</td><td><code>v-list-sys-ips</code></td><td><code>json</code></td><td>List server IPs.</td></tr>
<tr><td>IP addresses</td><td><code>v-add-sys-ip</code></td><td><code>IP NETMASK [INTERFACE] [USER] [STATUS] [NAME] [NAT]</code></td><td>Add and configure IP (admin).</td></tr>
<tr><td>Users</td><td><code>v-add-user</code></td><td><code>USER PASSWORD EMAIL [PACKAGE] [NAME]</code></td><td>Create user (admin).</td></tr>
<tr><td>Users</td><td><code>v-nexora-reseller</code></td><td><code>add-customer RESELLER USER PASSWORD EMAIL PACKAGE [NAME] | customers RESELLER json</code></td><td>Reseller customers.</td></tr>
</table>
<h2>Errors</h2>
<table><tr><th>Code</th><th>Meaning</th></tr><tr><td>0</td><td>OK</td></tr><tr><td>1</td><td>Missing arguments</td></tr><tr><td>2</td><td>Invalid value</td></tr><tr><td>3</td><td>Object does not exist</td></tr><tr><td>4</td><td>Object exists</td></tr><tr><td>9</td><td>Wrong credentials</td></tr><tr><td>10</td><td>Forbidden / key expired or disabled</td></tr><tr><td>11</td><td>Disabled</td></tr><tr><td>15</td><td>Connection failed</td></tr><tr><td>19</td><td>Update failed</td></tr></table>
<p>Error output is plain text, e.g. <code>Error: IP 192.0.2.99 is not configured on this server</code>.</p>
<h2>Examples – create a Node.js website</h2>
<h3>cURL</h3>
<pre>curl -s <?= tohtml($api_url) ?> \
  -d access_key=KEY -d secret_key=SECRET -d returncode=no \
  -d cmd=v-nexora-site -d arg1=add -d arg2=alice -d arg3=example.com \
  -d arg4=node -d arg5=192.0.2.10 -d arg6=22 -d arg7=1 -d arg8=512 -d arg9=</pre>
<h3>Python</h3>
<pre>import requests
r = requests.post("<?= tohtml($api_url) ?>", data={
    "access_key": "KEY", "secret_key": "SECRET", "returncode": "no",
    "cmd": "v-nexora-site", "arg1": "list", "arg2": "alice", "arg3": "json"})
print(r.json())</pre>
<h3>PHP</h3>
<pre>$ch = curl_init("<?= tohtml($api_url) ?>");
curl_setopt_array($ch, [CURLOPT_POST =&gt; true, CURLOPT_RETURNTRANSFER =&gt; true,
  CURLOPT_POSTFIELDS =&gt; http_build_query(["access_key" =&gt; "KEY", "secret_key" =&gt; "SECRET",
  "returncode" =&gt; "no", "cmd" =&gt; "v-nexora-site", "arg1" =&gt; "list", "arg2" =&gt; "alice", "arg3" =&gt; "json"])]);
var_dump(json_decode(curl_exec($ch), true));</pre>
<h3>JavaScript</h3>
<pre>const res = await fetch("<?= tohtml($api_url) ?>", { method: "POST",
  body: new URLSearchParams({ access_key: "KEY", secret_key: "SECRET", returncode: "no",
    cmd: "v-nexora-site", arg1: "list", arg2: "alice", arg3: "json" }) });
console.log(await res.json());</pre>
</div>
