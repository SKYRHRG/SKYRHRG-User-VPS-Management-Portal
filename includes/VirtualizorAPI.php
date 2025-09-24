<?php
/**
 * includes/VirtualizorAPI.php
 * Reworked: longer timeouts, better logging, async fallback option, robust success detection.
 *
 * Provider config can include:
 *  - hostname (string)
 *  - api_key (string)        // -> 'apikey' in earlier code
 *  - api_pass (string)       // -> 'apipass'
 *  - name (string)
 *  - port (int) optional, default 4083
 *  - debug (bool) optional
 *  - action_timeout (int) optional seconds (default 90)
 *  - reinstall_timeout (int) optional (default 180)
 *  - default_timeout (int) optional (default 30)
 *  - async_actions (bool) optional (default false) => fire-and-forget fallback on timeouts
 */

require_once __DIR__ . '/error_logger.php'; // optional; file gracefully handles missing

class VirtualizorAPI
{
    private $hostname;
    private $apiKey;
    private $apiPass;
    private $providerName;
    private $port;
    private $debug;
    private $actionTimeout;
    private $reinstallTimeout;
    private $defaultTimeout;
    private $asyncActions;

    public function __construct(array $providerConfig)
    {
        $this->hostname       = $providerConfig['hostname'] ?? ($providerConfig['host'] ?? '');
        $this->apiKey         = $providerConfig['api_key'] ?? $providerConfig['apikey'] ?? '';
        $this->apiPass        = $providerConfig['api_pass'] ?? $providerConfig['apipass'] ?? '';
        $this->providerName   = $providerConfig['name'] ?? ($providerConfig['provider_key'] ?? 'virtualizor');
        $this->port           = $providerConfig['port'] ?? 4083;
        $this->debug          = !empty($providerConfig['debug']);
        $this->actionTimeout  = !empty($providerConfig['action_timeout']) ? (int)$providerConfig['action_timeout'] : 90;
        $this->reinstallTimeout = !empty($providerConfig['reinstall_timeout']) ? (int)$providerConfig['reinstall_timeout'] : 180;
        $this->defaultTimeout = !empty($providerConfig['default_timeout']) ? (int)$providerConfig['default_timeout'] : 30;
        $this->asyncActions   = !empty($providerConfig['async_actions']);
    }

    /***********************
     * Logging helper
     ***********************/
    private function logLocal(string $title, $message, array $context = [])
    {
        // Try app logger if available
        if (function_exists('log_api_error')) {
            try { log_api_error($this->providerName, $title . ' - ' . $message, $context); } catch (\Throwable $e) {}
        }

        // Fallback file logging
        $file = __DIR__ . '/virtualizor_api_errors.log';
        $entry = [
            'time' => date('Y-m-d H:i:s'),
            'provider' => $this->providerName,
            'title' => $title,
            'message' => (string)$message,
            'context' => $context
        ];
        @file_put_contents($file, json_encode($entry, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND | LOCK_EX);

        if ($this->debug) {
            error_log("VirtualizorAPI: {$title} - {$message} -- " . json_encode($context));
        }
    }

    /**
     * makeRequest
     * - $usePost: if true, remaining params are sent as POST body (use for reinstall passwords)
     * - returns decoded array on success, or null on failure (but logs details)
     */
    private function makeRequest(array $params, int $timeout = null, bool $usePost = false)
    {
        if ($timeout === null) $timeout = $this->defaultTimeout;

        // Build URL params (always include auth in URL)
        $urlParams = ['api' => 'json', 'apikey' => $this->apiKey, 'apipass' => $this->apiPass];

        // Ensure act and svs are in URL (per Virtualizor docs)
        if (isset($params['act'])) { $urlParams['act'] = $params['act']; unset($params['act']); }
        if (isset($params['svs'])) { $urlParams['svs'] = $params['svs']; unset($params['svs']); }

        // If not using POST, merge all params into URL params
        $postFields = [];
        if ($usePost) {
            $postFields = $params; // send these in body
        } else {
            $urlParams = array_merge($urlParams, $params);
        }

        $query = http_build_query($urlParams);
        $url = "https://{$this->hostname}:{$this->port}/index.php?$query";

        // Prepare cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json', 'User-Agent: SKYRHRG-Portal/1.0']);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        if ($usePost && !empty($postFields)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
        }

        $raw = curl_exec($ch);
        $info = curl_getinfo($ch);
        $errNo = curl_errno($ch);
        $err = $errNo ? curl_error($ch) : null;
        curl_close($ch);

        // Log Curl info on debug
        if ($this->debug) $this->logLocal('cURL Info', 'curl_getinfo', $info);

        if ($errNo) {
            // If the call timed out or failed, log timing info & decide fallback
            $this->logLocal('cURL Error', $err ?? 'cURL error', ['url' => $url, 'http_code' => $info['http_code'] ?? 0, 'curl_info' => $info]);

            // If the provider requested async fallback, try to at least send the command (fire-and-forget)
            if ($this->asyncActions && !empty($urlParams['act']) && in_array($urlParams['act'], ['start','stop','restart','restart'])) {
                $ok = $this->sendAsyncGet($url); // returns true when request dispatched
                if ($ok) {
                    $this->logLocal('Async Fallback', 'Sent command using async fallback after cURL timeout', ['url'=>$url]);
                    // We can't verify the provider response but we've dispatched the command
                    // Return a minimal 'ack' shaped array so caller can treat it as accepted.
                    return ['done' => ['msg' => 'Command dispatched (async fallback)']];
                }
            }
            return null;
        }

        if ($raw === null || $raw === '') {
            // no body received
            $this->logLocal('Empty Response', 'Empty or null response', ['url' => $url, 'curl_info' => $info, 'raw' => substr((string)$raw, 0, 2000)]);
            return null;
        }

        // If HTML returned, likely login page or error page
        if (stripos($raw, '<html') !== false || stripos($raw, '<!doctype') !== false) {
            $this->logLocal('HTML Response', 'Expected JSON but received HTML (login page / error).', ['url'=>$url, 'http_code'=>$info['http_code'], 'raw_snippet'=>substr($raw,0,2000)]);
            return null;
        }

        $decoded = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->logLocal('JSON Decode Error', json_last_error_msg(), ['url' => $url, 'http_code' => $info['http_code'], 'raw' => substr($raw,0,2000)]);
            return null;
        }

        // If API returned 'error' key, log (but return decoded so caller can inspect)
        if (!empty($decoded['error'])) {
            $this->logLocal('API returned error', is_array($decoded['error']) ? implode(';', (array)$decoded['error']) : $decoded['error'], ['url'=>$url,'http_code'=>$info['http_code'],'response'=>$decoded]);
            return $decoded;
        }

        return $decoded;
    }

    /**
     * sendAsyncGet
     * Fire-and-forget GET using a short socket write (no response read).
     * Returns true when write succeeded.
     */
    private function sendAsyncGet(string $fullUrl): bool
    {
        // Parse the URL to build request
        $parts = parse_url($fullUrl);
        if (!$parts || empty($parts['host'])) return false;

        $scheme = $parts['scheme'] ?? 'https';
        $host = $parts['host'];
        $port = $parts['port'] ?? ($scheme === 'https' ? 443 : 80);
        $path = ($parts['path'] ?? '/') . (isset($parts['query']) ? '?' . $parts['query'] : '');

        // Open TLS socket with short timeout
        $transport = ($scheme === 'https') ? 'ssl' : 'tcp';
        $ctx = stream_context_create([
            'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
            'http' => []
        ]);

        $errno = 0; $errstr = '';
        $fp = @stream_socket_client("{$transport}://{$host}:{$port}", $errno, $errstr, 5, STREAM_CLIENT_CONNECT, $ctx);
        if (!$fp) {
            $this->logLocal('Async socket open failed', "$errstr ($errno)", ['host'=>$host,'port'=>$port]);
            return false;
        }

        stream_set_blocking($fp, false);
        $req = "GET {$path} HTTP/1.1\r\nHost: {$host}\r\nConnection: Close\r\nUser-Agent: SKYRHRG-Portal/1.0\r\nAccept: */*\r\n\r\n";
        @fwrite($fp, $req);
        // Close immediately — request has been dispatched to the remote side
        @fclose($fp);
        return true;
    }

    /***********************
     * Public API methods
     ***********************/

    public function getVpsDetails($vpsId)
    {
        if (empty($vpsId)) return null;

        // try listvs first (some Virtualizor versions return vs array)
        $resp = $this->makeRequest(['act' => 'listvs', 'svs' => (int)$vpsId], $this->defaultTimeout, false);
        if (is_array($resp)) {
            if (!empty($resp['vs']) && is_array($resp['vs'])) {
                if (isset($resp['vs'][$vpsId])) return $resp['vs'][$vpsId];
                return reset($resp['vs']);
            }
            if (!empty($resp['hostname']) || isset($resp['status'])) return $resp;
        }

        // fallback to vpsmanage
        $resp2 = $this->makeRequest(['act' => 'vpsmanage', 'svs' => (int)$vpsId], $this->defaultTimeout, false);
        if (is_array($resp2)) {
            if (!empty($resp2['vs']) && is_array($resp2['vs'])) {
                if (isset($resp2['vs'][$vpsId])) return $resp2['vs'][$vpsId];
                return reset($resp2['vs']);
            }
            if (!empty($resp2['hostname']) || isset($resp2['status'])) return $resp2;
        }

        $this->logLocal('getVpsDetails Failed', 'Could not find usable VPS details', ['vpsId' => $vpsId, 'resp1' => $resp, 'resp2' => $resp2 ?? null]);
        return null;
    }

    public function startVps($vpsId)
    {
        // If async fallback is configured and you prefer immediate return, set async_actions in provider config.
        $resp = $this->makeRequest(['act' => 'start', 'svs' => (int)$vpsId, 'do' => 1], $this->actionTimeout, false);
        return $this->isDoneResponse($resp);
    }

    public function stopVps($vpsId)
    {
        $resp = $this->makeRequest(['act' => 'stop', 'svs' => (int)$vpsId, 'do' => 1], $this->actionTimeout, false);
        return $this->isDoneResponse($resp);
    }

    public function rebootVps($vpsId)
    {
        $resp = $this->makeRequest(['act' => 'restart', 'svs' => (int)$vpsId, 'do' => 1], $this->actionTimeout, false);
        return $this->isDoneResponse($resp);
    }

    public function getOsTemplates()
    {
        $resp = $this->makeRequest(['act' => 'ostemplate'], $this->defaultTimeout, false);
        if (!$resp || !is_array($resp)) return [];

        foreach (['oslist','oses','ostemplates','os'] as $k) {
            if (!empty($resp[$k]) && is_array($resp[$k])) return $resp[$k];
        }
        return $resp;
    }

    public function reinstallOs($vpsId, $osId, $newPassword)
    {
        if (empty($vpsId) || empty($osId) || empty($newPassword)) {
            $this->logLocal('ReinstallOS Param Error', 'Missing param', ['vpsId' => $vpsId, 'osId' => $osId]);
            return false;
        }

        // Put auth & act in URL; send passwords via POST body (safer)
        $params = [
            'act' => 'ostemplate',
            'svs' => (int)$vpsId,
            'reinsos' => 1,
            'newos' => (int)$osId,
            'osid' => (int)$osId,
            // newpass/conf will go in POST
            'newpass' => $newPassword,
            'conf' => $newPassword,
        ];

        // makeRequest with usePost = true; longer timeout
        $resp = $this->makeRequest($params, $this->reinstallTimeout, true);
        return $this->isDoneResponse($resp);
    }

    public function changeVncPassword($vpsId, $newPassword)
    {
        $resp = $this->makeRequest(['act' => 'vncpass', 'svs' => (int)$vpsId, 'newpass' => $newPassword, 'conf' => $newPassword], $this->defaultTimeout, false);
        return $this->isDoneResponse($resp);
    }

    /**
     * Treat any non-empty 'done' (string|array|boolean|int) as success.
     * Also treat status==1 or 'running'/'online' as success.
     */
    private function isDoneResponse($resp): bool
    {
        if (!is_array($resp)) return false;

        if (array_key_exists('done', $resp) && !empty($resp['done'])) {
            return true;
        }

        if (array_key_exists('status', $resp)) {
            $s = $resp['status'];
            if ($s === 1 || $s === '1' || strtolower((string)$s) === 'running' || strtolower((string)$s) === 'online' || strtolower((string)$s) === 'on') {
                return true;
            }
        }

        if (!empty($resp['onboot'])) return true;

        // Log the failure for debugging — include short snippet
        $this->logLocal('API Done Check Failure', 'Response did not contain done/onboot/status=1', ['response_snippet' => array_slice($resp,0,20)]);
        return false;
    }
}
