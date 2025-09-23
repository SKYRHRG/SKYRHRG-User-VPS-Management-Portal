<?php
// fix_securedns_api.php
// Single-file diagnostic + request page for cp.securednscloud.com port issues.
// Usage: place in a web-accessible directory and open in browser.
// WARNING: Do not commit real API secrets to public repos. Rotate keys when exposed.

date_default_timezone_set('Asia/Kolkata');

function mask_secret($s, $show = 4) {
    if (!$s) return '';
    $len = strlen($s);
    if ($len <= $show) return str_repeat('*', $len);
    return substr($s,0,$show) . str_repeat('*', max(0, $len-$show- $show)) . substr($s,-$show);
}

function tcp_check($host, $port, $timeout = 3) {
    // Use stream socket client to attempt TCP connect
    $errno = 0; $errstr = '';
    $start = microtime(true);
    $fp = @stream_socket_client("tcp://{$host}:{$port}", $errno, $errstr, $timeout);
    $elapsed = round((microtime(true) - $start)*1000, 0);
    if ($fp) {
        fclose($fp);
        return ['ok'=>true, 'msg'=>"Connected (TCP) in {$elapsed} ms"];
    } else {
        return ['ok'=>false, 'msg'=>"TCP connect failed: {$errstr} ({$errno}) after {$timeout}s"];
    }
}

function do_curl_request($scheme, $host, $port, $postData, $timeout = 8) {
    $url = "{$scheme}://{$host}" . ($port ? ":{$port}" : '') . "/index.php";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    // Security: verify SSL for https; toggle if provider uses custom certs
    if ($scheme === 'https') {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    }
    $resp = curl_exec($ch);
    $errno = curl_errno($ch);
    $err = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return [
        'url'=>$url,
        'errno'=>$errno,
        'err'=>$err,
        'http_code'=>$http_code,
        'response'=>$resp
    ];
}

function append_log($line) {
    $file = __DIR__ . '/api_test.log';
    $time = date('Y-m-d H:i:s');
    error_log("{$time} | {$line}\n", 3, $file);
}

// Input handling: prefer POST but allow GET quick tests
$host = $_POST['host'] ?? ($_GET['host'] ?? 'cp.securednscloud.com');
$port = isset($_POST['port']) ? (int)$_POST['port'] : 4083;
$act = $_POST['act'] ?? 'listvs';
$svs = $_POST['svs'] ?? '4439';
$apikey = $_POST['apikey'] ?? '';
$apipass = $_POST['apipass'] ?? '';
$api_format = $_POST['api'] ?? 'json';
$do_send = isset($_POST['do_send']);

$diagnostics = [];
$results = null;
$tried_ports = [];
$fallback_ports = [$port, 443, 80]; // try original then common ports
$unique_ports = array_values(array_unique($fallback_ports));

if ($do_send) {
    // Masked logging
    append_log("REQUEST host={$host} ports=" . implode(',', $unique_ports) . " act={$act} svs={$svs} apikey=" . mask_secret($apikey) . " apipass=" . mask_secret($apipass));

    // DNS resolution
    $dns_ip = gethostbyname($host);
    if ($dns_ip === $host) {
        $diagnostics[] = "DNS: could NOT resolve hostname '{$host}' (gethostbyname returned '{$dns_ip}')";
    } else {
        $diagnostics[] = "DNS: resolved '{$host}' -> {$dns_ip}";
    }

    // Try ports
    foreach ($unique_ports as $p) {
        if (!$p) continue;
        $diag = tcp_check($host, $p, 3);
        $tried_ports[$p] = $diag;
        $diagnostics[] = "TCP port check {$host}:{$p} => " . $diag['msg'];
    }

    // If any tcp port returned ok, attempt cURL with matching scheme
    foreach ($unique_ports as $p) {
        $tcp = $tried_ports[$p] ?? null;
        // Decide scheme: use https for 443/4083 commonly, fallback to http for port 80
        $scheme = ($p === 80) ? 'http' : 'https';
        // If TCP check failed but user still wants to try, we still attempt the cURL (some networks respond differently)
        $post = [
            'act' => $act,
            'svs' => $svs,
            'api' => $api_format,
            'apikey' => $apikey,
            'apipass' => $apipass
        ];

        $curl_res = do_curl_request($scheme, $host, $p, $post, 10);

        // Save and present
        $results[$p] = $curl_res;
        $diagnostics[] = "cURL -> URL: {$curl_res['url']}  HTTP_CODE: {$curl_res['http_code']} errno: {$curl_res['errno']} err: {$curl_res['err']}";
        // stop early on successful 200/201 response
        if (in_array($curl_res['http_code'], [200,201]) && $curl_res['errno'] === 0) {
            $diagnostics[] = "Success: got HTTP {$curl_res['http_code']} on port {$p}";
            break;
        }
    }

    append_log("RESPONSE summary: " . json_encode(array_map(function($p,$r){ return "{$p}: http={$r['http_code']} err={$r['errno']}"; }, array_keys($results ?? []), array_values($results ?? []))));
}

?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>API connectivity tester — cp.securednscloud.com</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body{font-family:Inter,Arial,Helvetica,sans-serif;line-height:1.5;padding:18px;background:#f7f7fb;color:#111}
.container{max-width:900px;margin:0 auto;background:#fff;padding:18px;border-radius:10px;box-shadow:0 6px 18px rgba(0,0,0,0.06)}
h1{margin-top:0;font-size:20px}
label{display:block;margin-top:10px;font-weight:600}
input[type=text], input[type=number], input[type=password] {width:100%;padding:8px;margin-top:6px;border:1px solid #d6d6d8;border-radius:6px}
textarea{width:100%;height:120px;padding:8px;border-radius:6px;border:1px solid #d6d6d8}
button{margin-top:12px;padding:10px 14px;border-radius:8px;border:0;background:#2563eb;color:#fff;font-weight:700;cursor:pointer}
small.gray{color:#666}
.result{background:#fafafa;padding:12px;border-radius:8px;margin-top:14px;border:1px dashed #e5e7eb}
.diag{font-family:monospace;background:#111;color:#fff;padding:8px;border-radius:6px;overflow:auto}
.bad{color:#b91c1c}
.good{color:#065f46}
.mask{letter-spacing:2px}
</style>
</head>
<body>
<div class="container">
  <h1>API connectivity tester — <small class="gray">cp.securednscloud.com (diagnostics + cURL)</small></h1>

  <form method="post" autocomplete="off">
    <label>Host
      <input type="text" name="host" value="<?php echo htmlspecialchars($host); ?>">
    </label>
    <label>Primary Port
      <input type="number" name="port" value="<?php echo htmlspecialchars($port); ?>">
      <small class="gray">Script will also try fallback ports 443 and 80 automatically.</small>
    </label>
    <label>Action (act)
      <input type="text" name="act" value="<?php echo htmlspecialchars($act); ?>">
    </label>
    <label>svs
      <input type="text" name="svs" value="<?php echo htmlspecialchars($svs); ?>">
    </label>
    <label>API format (api)
      <input type="text" name="api" value="<?php echo htmlspecialchars($api_format); ?>">
    </label>
    <label>API Key
      <input type="text" name="apikey" value="<?php echo htmlspecialchars($apikey); ?>">
    </label>
    <label>API Password
      <input type="password" name="apipass" value="<?php echo htmlspecialchars($apipass); ?>">
      <small class="gray">The password will be masked in outputs.</small>
    </label>

    <button type="submit" name="do_send">Run diagnostic & send request</button>
  </form>

  <?php if ($do_send): ?>
    <div class="result">
      <h3>Summary</h3>
      <p><strong>Host:</strong> <?php echo htmlspecialchars($host); ?> &nbsp; <strong>Attempt ports:</strong> <?php echo htmlspecialchars(implode(',', $unique_ports)); ?></p>
      <p><strong>API Key:</strong> <span class="mask"><?php echo htmlspecialchars(mask_secret($apikey)); ?></span> &nbsp; <strong>API Pass:</strong> <span class="mask"><?php echo htmlspecialchars(mask_secret($apipass)); ?></span></p>

      <h4>Diagnostics</h4>
      <div class="diag">
      <?php foreach ($diagnostics as $d): ?>
        <?php
           $cls = (stripos($d,'could NOT') !== false || stripos($d,'failed') !== false || stripos($d,'Connection refused') !== false) ? 'bad' : 'good';
        ?>
        <div class="<?php echo $cls; ?>"><?php echo htmlspecialchars($d); ?></div>
      <?php endforeach; ?>
      </div>

      <h4 style="margin-top:10px">cURL results (per port)</h4>
      <?php if (!empty($results)): ?>
        <?php foreach ($results as $p => $r): ?>
          <div style="margin-top:8px;padding:8px;border-radius:6px;border:1px solid #eee">
            <strong>Port <?php echo htmlspecialchars($p); ?></strong>
            <div><small>URL: <?php echo htmlspecialchars($r['url']); ?></small></div>
            <div><small>HTTP code: <?php echo htmlspecialchars($r['http_code']); ?> &nbsp; cURL errno: <?php echo htmlspecialchars($r['errno']); ?></small></div>
            <?php if ($r['errno']): ?>
              <div class="bad">cURL error: <?php echo htmlspecialchars($r['err']); ?></div>
            <?php endif; ?>
            <details style="margin-top:6px">
              <summary>Response body (first 8k chars)</summary>
              <pre style="white-space:pre-wrap;max-height:320px;overflow:auto;background:#f3f4f6;padding:8px;border-radius:6px"><?php echo htmlspecialchars(substr($r['response'] ?? '',0,8192)); ?></pre>
            </details>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="bad">No cURL attempts were made.</div>
      <?php endif; ?>

      <h4 style="margin-top:10px">Suggested next steps</h4>
      <ul>
        <li>If TCP checks show "Connection refused": the remote service likely isn't listening on that port. Contact the API provider and confirm the correct port and whether your IP needs whitelisting.</li>
        <li>If DNS failed to resolve: check your server's DNS or /etc/hosts. Try `dig` or `nslookup` from the server.</li>
        <li>If cURL shows TLS errors: confirm whether the remote uses a custom certificate or requires specific TLS versions. You can disable SSL verification for debugging (not recommended in production).</li>
        <li>Check provider status page / announcements to see if maintenance is happening.</li>
        <li>If provider expects a different endpoint path (not /index.php), change the script accordingly.</li>
      </ul>

    </div>
  <?php endif; ?>

  <div style="margin-top:14px;color:#666;font-size:13px">
    <strong>Log file:</strong> <code><?php echo __DIR__ . '/api_test.log'; ?></code> (appended each run). Rotate/remove this file as needed.
  </div>

</div>
</body>
</html>
