<div class="container nx-docs">
<style>.nx-docs pre{background:var(--color-background);border:1px solid #202020;padding:12px;overflow:auto;margin:8px 0 20px;font-size:13px}.nx-docs table{width:100%;border-collapse:collapse;margin-bottom:24px}.nx-docs td,.nx-docs th{border:1px solid #202020;padding:6px 8px;text-align:left;vertical-align:top;font-size:14px}.nx-docs h2{margin:28px 0 8px}</style>
<h1>API Documentation</h1>
<nav class="u-mb20"><a href="#quickstart">Quick start</a> · <a href="#keys">API keys</a> · <a href="#reseller">Reseller integration</a> · <a href="#endpoints">Endpoints</a> · <a href="#errors">Errors</a> · <a href="#examples">Examples</a> · <a href="#security">Security</a></nav>

<h2 id="quickstart">Quick start in 4 steps</h2>
<ol class="u-mb20" style="padding-left:20px;line-height:1.9">
<li>Open <a href="/list/access-key/">API Keys</a> → <b>Add Access Key</b>.</li>
<li>Pick a permission group: <code>nexora-websites</code> (own websites, SSL, databases, DNS), <code>nexora-reseller</code> (resellers: customers + their websites) or <code>nexora-admin</code> (admins only).</li>
<li>Copy <b>Access Key</b> and <b>Secret Key</b>. The secret is shown <b>only once</b> – NEXORA stores just a SHA-256 hash. Lost it? Delete the key and create a new one.</li>
<li>Send a <code>POST</code> to the API URL below with <code>access_key</code>, <code>secret_key</code>, <code>cmd</code> and <code>arg1…</code>. Test it:</li>
</ol>
<pre>curl -s <?= tohtml($api_url) ?> -d access_key=KEY -d secret_key=SECRET \
  -d cmd=v-nexora-site -d arg1=list -d arg2=<?= tohtml($_SESSION["user"] ?? "USER") ?> -d arg3=json</pre>

<h2 id="keys">How API keys work</h2>
<ul style="padding-left:20px;line-height:1.9" class="u-mb20">
<li><b>Scope:</b> a key of a non-admin can only act on its own account. The API compares the command's USER argument with the key owner and rejects everything else.</li>
<li><b>Permissions:</b> a key may only run the commands of its permission group.</li>
<li><b>Expiry / disable / last use:</b> expired or disabled keys are rejected (error 10); last use is recorded.</li>
<li><b>Never</b> put keys in browser JavaScript or public repositories. Call the API from your backend only.</li>
</ul>

<h2 id="reseller">Reseller integration (billing system, shop, own portal)</h2>
<p>As a reseller you can automate everything for your customers with one key from the group <code>nexora-reseller</code>. Your own username is always <code>arg2</code>; NEXORA checks on the server that the customer belongs to you.</p>
<table><tr><th>Goal</th><th>cmd</th><th>arg1</th><th>arg2</th><th>arg3 …</th></tr>
<tr><td>Create customer</td><td><code>v-nexora-reseller</code></td><td><code>add-customer</code></td><td>RESELLER</td><td>USER PASSWORD EMAIL PACKAGE [NAME]</td></tr>
<tr><td>List customers</td><td><code>v-nexora-reseller</code></td><td><code>customers</code></td><td>RESELLER</td><td><code>json</code></td></tr>
<tr><td>Suspend / unsuspend / delete</td><td><code>v-nexora-reseller</code></td><td><code>suspend</code> / <code>unsuspend</code> / <code>delete</code></td><td>RESELLER</td><td>CUSTOMER</td></tr>
<tr><td>Create website for customer</td><td><code>v-nexora-reseller</code></td><td><code>site</code></td><td>RESELLER</td><td>CUSTOMER <code>add</code> DOMAIN TYPE IP [VERSION] [CPU] [MEM]…</td></tr>
<tr><td>Customer websites</td><td><code>v-nexora-reseller</code></td><td><code>site</code></td><td>RESELLER</td><td>CUSTOMER <code>list</code> <code>json</code></td></tr>
<tr><td>Start/stop/limits</td><td><code>v-nexora-reseller</code></td><td><code>site</code></td><td>RESELLER</td><td>CUSTOMER <code>stop</code> DOMAIN</td></tr>
</table>
<p><b>Typical flow</b> when an order is paid: 1) <code>add-customer</code> → 2) <code>site … add</code> → 3) send the customer their login. On cancellation: <code>suspend</code>; after the retention period: <code>delete</code>.</p>
<h3>Python – complete order flow</h3>
<pre>import requests, secrets

API = "<?= tohtml($api_url) ?>"
AUTH = {"access_key": "KEY", "secret_key": "SECRET"}
RESELLER = "myreseller"

def call(cmd, *args):
    data = {**AUTH, "cmd": cmd, "returncode": "no"}
    data.update({f"arg{i}": a for i, a in enumerate(args, 1)})
    r = requests.post(API, data=data, timeout=120)
    if r.status_code != 200 or r.text.startswith("Error"):
        raise RuntimeError(r.text)
    return r.text

password = secrets.token_urlsafe(16)
call("v-nexora-reseller", "add-customer", RESELLER, "shop42", password, "owner@example.com", "default", "Shop 42")
call("v-nexora-reseller", "site", RESELLER, "shop42", "add", "shop42.example.com", "wordpress", "192.0.2.10", "8.3", "1", "1024", "", "yes", "yes", "no")
print(call("v-nexora-reseller", "site", RESELLER, "shop42", "list", "json"))</pre>
<h3>PHP – same flow</h3>
<pre>function nexora(string $cmd, array $args = []): string {
    $data = ["access_key" =&gt; "KEY", "secret_key" =&gt; "SECRET", "cmd" =&gt; $cmd, "returncode" =&gt; "no"];
    foreach (array_values($args) as $i =&gt; $a) { $data["arg" . ($i + 1)] = $a; }
    $ch = curl_init("<?= tohtml($api_url) ?>");
    curl_setopt_array($ch, [CURLOPT_POST =&gt; true, CURLOPT_RETURNTRANSFER =&gt; true, CURLOPT_TIMEOUT =&gt; 120,
        CURLOPT_POSTFIELDS =&gt; http_build_query($data)]);
    $out = curl_exec($ch);
    if ($out === false || str_starts_with($out, "Error")) { throw new RuntimeException((string) $out); }
    return $out;
}
nexora("v-nexora-reseller", ["add-customer", "myreseller", "shop42", bin2hex(random_bytes(12)), "owner@example.com", "default"]);
nexora("v-nexora-reseller", ["site", "myreseller", "shop42", "add", "shop42.example.com", "php", "192.0.2.10", "8.3"]);</pre>
<h3>Node.js</h3>
<pre>async function nexora(cmd, ...args) {
  const body = new URLSearchParams({ access_key: process.env.NX_KEY, secret_key: process.env.NX_SECRET, cmd, returncode: "no" });
  args.forEach((a, i) =&gt; body.set(`arg${i + 1}`, a));
  const res = await fetch("<?= tohtml($api_url) ?>", { method: "POST", body });
  const text = await res.text();
  if (!res.ok || text.startsWith("Error")) throw new Error(text);
  return text;
}
await nexora("v-nexora-reseller", "customers", "myreseller", "json");</pre>

<h2 id="api-url">API URL</h2><pre><?= tohtml($api_url) ?></pre>
<h2>Request fields</h2>

<p>Create a key under <a href="/list/access-key/">Settings → API Management</a>. The secret is shown once and stored only as a SHA-256 hash. Keys can expire, be disabled and are bound to permission groups (e.g. <code>nexora-websites</code>, <code>nexora-admin</code>). Each request is a <code>POST</code> with form fields:</p>
<table><tr><th>Field</th><th>Description</th></tr>
<tr><td><code>access_key</code></td><td>Access key ID</td></tr><tr><td><code>secret_key</code></td><td>Secret</td></tr>
<tr><td><code>cmd</code></td><td>Command name</td></tr><tr><td><code>arg1</code> … <code>arg9</code></td><td>Positional arguments</td></tr>
<tr><td><code>returncode</code></td><td><code>yes</code> = only return exit code, <code>no</code> = return output</td></tr></table>
<h2 id="endpoints">Endpoints</h2>
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
<h2 id="errors">Errors</h2>
<table><tr><th>Code</th><th>Meaning</th></tr><tr><td>0</td><td>OK</td></tr><tr><td>1</td><td>Missing arguments</td></tr><tr><td>2</td><td>Invalid value</td></tr><tr><td>3</td><td>Object does not exist</td></tr><tr><td>4</td><td>Object exists</td></tr><tr><td>9</td><td>Wrong credentials</td></tr><tr><td>10</td><td>Forbidden / key expired or disabled</td></tr><tr><td>11</td><td>Disabled</td></tr><tr><td>15</td><td>Connection failed</td></tr><tr><td>19</td><td>Update failed</td></tr></table>
<p>Error output is plain text, e.g. <code>Error: IP 192.0.2.99 is not configured on this server</code>.</p>
<h2 id="examples">Examples – create a Node.js website</h2>
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
<h2 id="security">Security checklist</h2>
<ul style="padding-left:20px;line-height:1.9">
<li>Use HTTPS only; keep keys in environment variables or a secret store.</li>
<li>One key per integration, with the smallest permission group; set an expiry date.</li>
<li>Rotate keys regularly: create new key → deploy → delete old key.</li>
<li>Check the response for <code>Error</code> and retry only idempotent calls (list/status).</li>
</ul>
</div>
