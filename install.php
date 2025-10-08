<?php
// Simple one-time installer for production deployment
// 1) Writes config/env.php with DB creds and APP_ENV
// 2) Optionally imports database schema+migrations

if (file_exists(__DIR__ . '/../config/env.php')) {
    http_response_code(403);
    echo 'Installer already completed.';
    exit;
}

function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES); }

$step = $_SERVER['REQUEST_METHOD'] === 'POST' ? 'write' : 'form';

if ($step === 'write') {
    $dbHost = trim($_POST['db_host'] ?? 'localhost');
    $dbName = trim($_POST['db_name'] ?? 'ticko');
    $dbUser = trim($_POST['db_user'] ?? 'root');
    $dbPass = trim($_POST['db_pass'] ?? '');
    $appEnv = trim($_POST['app_env'] ?? 'production');
    $import = isset($_POST['import_schema']);

    // Create env.php
    $envPhp = "<?php\nreturn [\n    'DB_HOST' => '" . addslashes($dbHost) . "',\n    'DB_NAME' => '" . addslashes($dbName) . "',\n    'DB_USER' => '" . addslashes($dbUser) . "',\n    'DB_PASS' => '" . addslashes($dbPass) . "',\n    'APP_ENV' => '" . addslashes($appEnv) . "',\n];\n";
    $envPath = __DIR__ . '/../config/env.php';
    @file_put_contents($envPath, $envPhp);

    $msg = 'Configuration saved.';

    if ($import) {
        try {
            $dsn = 'mysql:host=' . $dbHost . ';charset=utf8mb4';
            $pdo = new PDO($dsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
            $pdo->exec('CREATE DATABASE IF NOT EXISTS `' . str_replace('`','',$dbName) . '` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
            $pdo->exec('USE `' . str_replace('`','',$dbName) . '`');
            // Import schema (if exists)
            $schema = __DIR__ . '/../database/schema.sql';
            if (file_exists($schema)) { $pdo->exec(file_get_contents($schema)); }
            // Import migrations
            $migDir = __DIR__ . '/../database/migrations';
            if (is_dir($migDir)) {
                foreach (glob($migDir . '/*.sql') as $file) {
                    $pdo->exec(file_get_contents($file));
                }
            }
            $msg .= ' Database imported.';
        } catch (Throwable $e) {
            $msg .= ' Database import failed: ' . $e->getMessage();
        }
    }

    echo '<!doctype html><meta charset="utf-8"><style>body{font-family:system-ui;background:#0b0b0b;color:#e5e7eb;padding:2rem}a{color:#38bdf8}</style>';
    echo '<h1>Installer</h1><p>' . h($msg) . '</p><p><a href="./">Go to site</a></p>';
    exit;
}

// Render form
echo '<!doctype html><meta charset="utf-8"><style>body{font-family:system-ui;background:#0b0b0b;color:#e5e7eb;padding:2rem}input,select{background:#0f0f10;border:1px solid #374151;color:#e5e7eb;border-radius:.5rem;padding:.5rem .75rem;width:100%}label{display:block;margin:.5rem 0 .25rem}button{background:#ef4444;color:#fff;border:none;border-radius:.5rem;padding:.5rem 1rem;margin-top:1rem}form{max-width:520px}</style>';
echo '<h1>ShikaTicket Installer</h1>';
echo '<form method="post">';
echo '<label>DB Host</label><input name="db_host" value="' . h($_SERVER['DB_HOST'] ?? 'localhost') . '">';
echo '<label>DB Name</label><input name="db_name" value="ticko">';
echo '<label>DB User</label><input name="db_user" value="root">';
echo '<label>DB Password</label><input type="password" name="db_pass" value="">';
echo '<label>Environment</label><select name="app_env"><option value="production" selected>production</option><option value="development">development</option></select>';
echo '<label style="display:flex;align-items:center;gap:.5rem"><input type="checkbox" name="import_schema" style="width:auto"> Import schema and migrations</label>';
echo '<button type="submit">Install</button>';
echo '</form>';

