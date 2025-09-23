<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 3.1
 * Author: SKYRHRG Technologies Systems
 *
 * Virtualizor End-User API Client
 */

// Use require_once to prevent redeclaration errors.
require_once __DIR__ . '/error_logger.php';

class VirtualizorAPI
{
    private $hostname;
    private $apiKey;
    private $apiPass;
    private $providerName;

    public function __construct(array $providerConfig)
    {
        $this->hostname = $providerConfig['hostname'];
        $this->apiKey = $providerConfig['api_key'];
        $this->apiPass = $providerConfig['api_pass'];
        $this->providerName = $providerConfig['name'];
    }

    private function makeRequest(array $postData)
    {
        $postData['api'] = 'json';
        $postData['apikey'] = $this->apiKey;
        $postData['apipass'] = $this->apiPass;

        $url = "https://{$this->hostname}:4083/index.php";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            log_api_error($this->providerName, "cURL Error: " . $error, $postData);
            return null;
        }
        
        curl_close($ch);

        $decodedResponse = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            log_api_error($this->providerName, "Failed to decode JSON response.", ['raw_response' => $response]);
            return null;
        }

        if (isset($decodedResponse['error']) && !empty($decodedResponse['error'])) {
            $errorMessage = is_array($decodedResponse['error']) ? implode(', ', $decodedResponse['error']) : $decodedResponse['error'];
            log_api_error($this->providerName, "API Error: " . $errorMessage, $postData);
            return null;
        }
        
        return $decodedResponse;
    }

    // ... (rest of the functions like getVpsDetails, startVps, etc. remain the same) ...

     /**
     * Fetches all details for a specific VPS.
     *
     * @param int $vpsId The Virtualizor ID of the VPS.
     * @return array|null An array of VPS details or null on failure.
     */
    public function getVpsDetails($vpsId)
    {
        $response = $this->makeRequest([
            'act' => 'listvs',
            'svs' => $vpsId,
        ]);
        
        // The listvs action returns the VPS details under the 'vs' key.
        return $response['vs'][$vpsId] ?? null;
    }

    /**
     * Starts a VPS.
     *
     * @param int $vpsId The Virtualizor ID of the VPS.
     * @return bool True on success, false on failure.
     */
    public function startVps($vpsId)
    {
        $response = $this->makeRequest(['act' => 'start', 'svs' => $vpsId]);
        return isset($response['done']);
    }

    /**
     * Stops a VPS.
     *
     * @param int $vpsId The Virtualizor ID of the VPS.
     * @return bool True on success, false on failure.
     */
    public function stopVps($vpsId)
    {
        $response = $this->makeRequest(['act' => 'stop', 'svs' => $vpsId]);
        return isset($response['done']);
    }

    /**
     * Reboots a VPS.
     *
     * @param int $vpsId The Virtualizor ID of the VPS.
     * @return bool True on success, false on failure.
     */
    public function rebootVps($vpsId)
    {
        $response = $this->makeRequest(['act' => 'restart', 'svs' => $vpsId]);
        return isset($response['done']);
    }

    /**
     * Fetches the available OS templates for re-installation.
     *
     * @return array|null An array of available OS templates or null on failure.
     */
    public function getOsTemplates()
    {
        $response = $this->makeRequest(['act' => 'ostemplate']);
        return $response['oslist'] ?? null;
    }

    /**
     * Re-installs the OS on a VPS.
     *
     * @param int $vpsId The Virtualizor ID of the VPS.
     * @param int $osId The ID of the OS template to install.
     * @param string $newPassword The new root password for the server.
     * @return bool True on success, false on failure.
     */
    public function reinstallOs($vpsId, $osId, $newPassword)
    {
        $response = $this->makeRequest([
            'act' => 'rebuild',
            'svs' => $vpsId,
            'osid' => $osId,
            'newpass' => $newPassword,
            'conf' => 1, // Confirm the action
        ]);
        return isset($response['done']);
    }

    /**
     * Changes the VNC password for a VPS.
     *
     * @param int $vpsId The Virtualizor ID of the VPS.
     * @param string $newPassword The new VNC password.
     * @return bool True on success, false on failure.
     */
    public function changeVncPassword($vpsId, $newPassword)
    {
        $response = $this->makeRequest([
            'act' => 'vncpass',
            'svs' => $vpsId,
            'newpass' => $newPassword,
            'conf' => 1, // Confirm the action
        ]);
        return isset($response['done']);
    }
}