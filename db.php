<?php
// ============================================================
// DATABASE CONNECTION FILE (Environment-Aware)
// College Event Management System
// Compatible with:
// - LOCAL: XAMPP / Apache / MySQL (localhost, root, empty pwd)
// - PRODUCTION: Vercel / Cloud MySQL (TiDB Cloud, Aiven, etc.)
// ============================================================

// 1. Read environment variables (Production) with fallbacks to Local (XAMPP)
$db_host = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? 'localhost');
$db_port = (int)(getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? 3306));
$db_user = getenv('DB_USERNAME') ?: ($_ENV['DB_USERNAME'] ?? 'root');
$db_pass = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : ($_ENV['DB_PASSWORD'] ?? '');
$db_name = getenv('DB_DATABASE') ?: ($_ENV['DB_DATABASE'] ?? 'college_event_management');
$db_ssl  = getenv('DB_SSL_CA') ?: ($_ENV['DB_SSL_CA'] ?? null);

// Determine if we are running with a remote cloud database
$is_cloud_db = ($db_host !== 'localhost' && $db_host !== '127.0.0.1');

// Disable strict exceptions so connection errors can be handled gracefully
mysqli_report(MYSQLI_REPORT_OFF);

// 2. Initialize MySQLi
$conn = mysqli_init();

if (!$conn) {
    die("<div style='padding:20px;margin:20px;background:#FEE2E2;color:#DC2626;border-radius:8px;font-family:sans-serif;'>
        <h3>MySQLi Initialization Failed</h3>
        <p>PHP mysqli extension is not available or failed to initialize.</p>
    </div>");
}

// Set connection timeout (10 seconds for cloud network calls)
mysqli_options($conn, MYSQLI_OPT_CONNECT_TIMEOUT, 10);

// For Cloud databases (TiDB Cloud, Aiven, PlanetScale, etc.), SSL/TLS is required
if ($is_cloud_db) {
    if ($db_ssl && file_exists($db_ssl)) {
        mysqli_ssl_set($conn, NULL, NULL, $db_ssl, NULL, NULL);
    } else {
        // Standard Linux CA certificates path (Ubuntu/Debian in Docker)
        $system_ca = '/etc/ssl/certs/ca-certificates.crt';
        if (file_exists($system_ca)) {
            mysqli_ssl_set($conn, NULL, NULL, $system_ca, NULL, NULL);
        }
    }
}

// 3. Connect to Database
$connected = false;
$error_msg = '';

try {
    $flags = $is_cloud_db ? MYSQLI_CLIENT_SSL : 0;
    $connected = @mysqli_real_connect($conn, $db_host, $db_user, $db_pass, $db_name, $db_port, NULL, $flags);

    // Fallback attempt without explicit SSL flags if provider does opportunistic TLS
    if (!$connected && $is_cloud_db) {
        $connected = @mysqli_real_connect($conn, $db_host, $db_user, $db_pass, $db_name, $db_port);
    }
    if (!$connected) {
        $error_msg = mysqli_connect_error();
    }
} catch (Throwable $e) {
    $connected = false;
    $error_msg = $e->getMessage();
}

// 4. Verify Connection
if (!$connected) {
    if (empty($error_msg)) {
        $error_msg = mysqli_connect_error() ?: "Unable to connect to MySQL server at $db_host:$db_port";
    }
    $env_label = $is_cloud_db ? "Production Cloud Database ($db_host:$db_port)" : "Local XAMPP (localhost:3306)";
    
    die("<div style='padding:24px;margin:20px auto;max-width:600px;background:#FEF2F2;color:#991B1B;border:1px solid #F87171;border-radius:10px;font-family:system-ui,-apple-system,sans-serif;'>
        <h2 style='margin-top:0;'>⚠️ Database Connection Failed</h2>
        <p><strong>Environment:</strong> {$env_label}</p>
        <p><strong>Database:</strong> " . htmlspecialchars($db_name) . "</p>
        <p><strong>Error:</strong> " . htmlspecialchars($error_msg) . "</p>
        <hr style='border:none;border-top:1px solid #FCA5A5;margin:16px 0;'>
        <h4 style='margin-bottom:8px;'>Troubleshooting Steps:</h4>
        <ul style='padding-left:20px;line-height:1.6;'>
            " . ($is_cloud_db ? "
                <li>Ensure environment variables <code>DB_HOST</code>, <code>DB_PORT</code>, <code>DB_USERNAME</code>, <code>DB_PASSWORD</code>, <code>DB_DATABASE</code> are set in Vercel.</li>
                <li>Make sure your cloud database allows incoming traffic from all IP addresses (<code>0.0.0.0/0</code>).</li>
                <li>Run <code>database.sql</code> or visit <code>init_db.php</code> to initialize tables.</li>
            " : "
                <li>Make sure XAMPP <strong>Apache</strong> and <strong>MySQL</strong> services are running.</li>
                <li>Ensure the database <code>college_event_management</code> exists.</li>
                <li>Import <code>database.sql</code> via phpMyAdmin at <a href='http://localhost/phpmyadmin/'>http://localhost/phpmyadmin/</a>.</li>
            ") . "
        </ul>
    </div>");
}

// 5. Set character set to UTF-8
mysqli_set_charset($conn, "utf8mb4");

// 6. Set SQL mode for compatibility across all MySQL / MariaDB / TiDB versions
@mysqli_query($conn, "SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION'");

// 7. Session Configuration & Initialization
if (session_status() === PHP_SESSION_NONE) {
    // If running in container/serverless and default session dir is not writable, fallback to /tmp
    $current_save_path = session_save_path();
    if (!empty($current_save_path) && !is_writable($current_save_path)) {
        @session_save_path('/tmp');
    }
    
    // Configure secure session cookie defaults
    $is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
                (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
                
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $is_https,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    
    session_start();
}
?>
