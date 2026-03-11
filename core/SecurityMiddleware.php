<?php

/**
 * Security Middleware to defend against HTTP Flood DDoS
 * 
 * Includes 2 Modes for Surveys:
 * - Rule-based Mode (> 20 requests per minute blocks the IP)
 * - Machine Learning Mode (Hits Python Defense API)
 */
class SecurityMiddleware
{

    // Toggle Mode
    // true = Call the ML Python API
    // false = Simple Rule-based logic
    const USE_MACHINE_LEARNING = true;

    // ML API endpoint (matches Flask default port)
    const ML_API_URL = "http://127.0.0.1:5000/predict";

    // Config: Maximum history size to track in session to keep it lightweight
    const MAX_HISTORY = 30;

    public static function check()
    {
        // Start session if not existing, need it for storing history
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $ip = $_SERVER['REMOTE_ADDR'];
        $now = microtime(true);

        // Ensure request history structure exists
        if (!isset($_SESSION['request_history'])) {
            $_SESSION['request_history'] = [];
        }

        // Add current request to history
        $_SESSION['request_history'][] = $now;

        // Purge records older than 60 seconds to keep array small
        $_SESSION['request_history'] = array_filter($_SESSION['request_history'], function ($timestamp) use ($now) {
            return ($now - $timestamp) <= 60;
        });

        // Re-index array
        $_SESSION['request_history'] = array_values($_SESSION['request_history']);

        // Keep only max items to prevent memory bloat over long sessions
        if (count($_SESSION['request_history']) > self::MAX_HISTORY) {
            $_SESSION['request_history'] = array_slice($_SESSION['request_history'], -self::MAX_HISTORY);
        }

        // Calculate features
        $total_recent = count($_SESSION['request_history']);
        $avg_interval = 10.0; // Default if only 1 request

        if ($total_recent > 1) {
            // Find distinct time intervals between consecutive requests
            $intervals = [];
            for ($i = 1; $i < $total_recent; $i++) {
                $intervals[] = $_SESSION['request_history'][$i] - $_SESSION['request_history'][$i - 1];
            }
            // Average interval calculation
            $avg_interval = array_sum($intervals) / count($intervals);
        }

        // Count how many requests in the last 10 seconds (for ML model parity)
        $requests_last_10s = 0;
        foreach ($_SESSION['request_history'] as $t) {
            if (($now - $t) <= 10) {
                $requests_last_10s++;
            }
        }

        $action = "allow";

        if (self::USE_MACHINE_LEARNING) {
            // MODE: Machine Learning Defense API (Flask)
            // Note: Since feature extract expects 10s groupings, we pass requests_last_10s
            $payload = json_encode([
                "ip" => $ip,
                "recent_requests_count" => $requests_last_10s,
                "avg_interval" => $avg_interval
            ]);

            $ch = curl_init(self::ML_API_URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, 500); // 500ms max timeout to not delay legit users if ML is down

            $response = curl_exec($ch);

            if ($response !== false) {
                $decoded = json_decode($response, true);
                if (isset($decoded['status']) && $decoded['status'] === 'block') {
                    $action = "block";
                }
            }
            else {
            // If Python API is unreachable or times out, fallback to allow or rule-based.
            // Depending on strictness, we'll allow it for now.
            // error_log("ML API unreachable: " . curl_error($ch));
            }
            curl_close($ch);

        }
        else {
            // MODE: Simple Rule-Based
            // Block if more than 20 requests in the rolling 60s window
            if ($total_recent > 20) {
                $action = "block";
            }
        }

        // Only log when there is more than 1 request to avoid spamming the log for single unique visitors
        if ($total_recent > 1 && $requests_last_10s > 0) {
            try {
                // Ensure model loading works here (AutoLoader should handle it since DB is initialized)
                $securityLog = new SecurityLog();
                $securityLog->logAction($ip, $requests_last_10s, $avg_interval, $action);
            }
            catch (Exception $e) {
            // Ignore logging errors so we don't break the app
            }
        }

        // Action Handling
        if ($action === "block") {
            http_response_code(403);
            die("<h1>403 Forbidden</h1><p>Hệ thống phát hiện dấu hiệu tấn công từ IP của bạn.</p>");
        }
    }
}
