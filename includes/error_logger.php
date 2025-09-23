<?php
/**
 * Project: SKYRHRG User Management Portal
 * Version: 3.1
 * Author: SKYRHRG Technologies Systems
 *
 * API Error Logging Utility
 */

if (!function_exists('log_api_error')) {
    /**
     * Logs an error message to a dedicated API log file.
     *
     * @param string $component The component where the error occurred (e.g., 'Hyper Sonic API', 'Database').
     * @param string $errorMessage The detailed error message to be logged.
     * @param array $context Optional additional data to log.
     */
    function log_api_error($component, $errorMessage, $context = []) {
        $logFile = __DIR__ . '/../logs/virtualizor_api_errors.log';
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] | Component: $component | Error: $errorMessage";

        if (!empty($context)) {
            $logEntry .= " | Context: " . json_encode($context);
        }

        $logEntry .= PHP_EOL;

        // Ensure the logs directory exists and is writable.
        if (!is_dir(__DIR__ . '/../logs')) {
            mkdir(__DIR__ . '/../logs', 0755, true);
        }

        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
}
?>