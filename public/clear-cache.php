<?php
/**
 * --------------------------------------------------------------------------
 * Standalone Production Laravel Cache Cleaner Tool
 * --------------------------------------------------------------------------
 * ⚠️ WARNING: Delete this file immediately after use.
 * --------------------------------------------------------------------------
 */

// SECURITY TOKEN PROTECTION (Secret Authentication Token)
define('SECRET_TOKEN', 'TW_ProdCache_9f8a3b7c4d1e2f506a7b8c9d01234567');

$providedToken = $_GET['token'] ?? '';

if (empty($providedToken) || !hash_equals(SECRET_TOKEN, (string)$providedToken)) {
    http_response_code(403);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>403 Forbidden - Access Denied</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background: #0f172a; color: #f8fafc; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .card { background: #1e293b; padding: 2.5rem; border-radius: 12px; border: 1px solid #334155; text-align: center; max-width: 420px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.5); }
        h1 { color: #ef4444; font-size: 1.8rem; margin: 0 0 0.5rem 0; }
        p { color: #94a3b8; font-size: 0.95rem; margin: 0; }
    </style>
</head>
<body>
    <div class="card">
        <h1>403 Forbidden</h1>
        <p>Invalid or missing authentication token. Access is strictly denied.</p>
    </div>
</body>
</html>';
    exit;
}

// LOCATE LARAVEL CORE INSTALLATION FOLDER
$possibleCorePaths = [
    __DIR__ . '/../laravel_core',
    __DIR__ . '/laravel_core',
    dirname(__DIR__) . '/laravel_core',
    __DIR__ . '/..',
    __DIR__,
];

$laravelPath = null;
foreach ($possibleCorePaths as $path) {
    if ($path && file_exists($path . '/vendor/autoload.php') && file_exists($path . '/bootstrap/app.php')) {
        $laravelPath = realpath($path);
        break;
    }
}

if (!$laravelPath) {
    http_response_code(500);
    die('Error: Could not locate Laravel core installation directory containing vendor/autoload.php and bootstrap/app.php');
}

// BOOTSTRAP LARAVEL APPLICATION
require_once $laravelPath . '/vendor/autoload.php';
$app = require_once $laravelPath . '/bootstrap/app.php';

use Illuminate\Contracts\Console\Kernel;
use Symfony\Component\Console\Output\BufferedOutput;

// EXECUTE OPTIMIZE:CLEAR VIA CONSOLE KERNEL (NO SHELL / EXEC CALLS)
$kernel = $app->make(Kernel::class);
$outputBuffer = new BufferedOutput();

$exitCode = $kernel->call('optimize:clear', [], $outputBuffer);
$outputContent = $outputBuffer->fetch();

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Cache Clear Tool</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background: #0f172a; color: #f8fafc; margin: 0; padding: 2rem; }
        .container { max-width: 750px; margin: 0 auto; }
        .alert-warning { background-color: #7f1d1d; color: #fca5a5; border: 1px solid #991b1b; padding: 1rem 1.25rem; border-radius: 8px; font-weight: 700; font-size: 0.95rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; }
        .card { background: #1e293b; border: 1px solid #334155; border-radius: 12px; padding: 2rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5); }
        .header { display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #334155; padding-bottom: 1rem; margin-bottom: 1.5rem; }
        h1 { font-size: 1.5rem; margin: 0; color: #38bdf8; }
        .badge-success { background: #065f46; color: #34d399; padding: 0.35rem 0.85rem; border-radius: 9999px; font-weight: 700; font-size: 0.85rem; border: 1px solid #059669; }
        .checklist { list-style: none; padding: 0; margin: 0 0 1.5rem 0; }
        .checklist li { padding: 0.5rem 0; font-size: 1rem; color: #e2e8f0; display: flex; align-items: center; gap: 0.75rem; border-bottom: 1px dashed #334155; }
        .checklist li:last-child { border-bottom: none; }
        .icon-check { color: #34d399; font-weight: bold; }
        .output-box { background: #020617; border: 1px solid #334155; border-radius: 8px; padding: 1.25rem; font-family: monospace; font-size: 0.9rem; color: #a7f3d0; white-space: pre-wrap; word-break: break-word; }
        .exit-code { margin-top: 1.5rem; font-weight: 700; color: #94a3b8; font-size: 0.9rem; text-align: right; }
    </style>
</head>
<body>
    <div class="container">
        <div class="alert-warning">
            ⚠️ WARNING: Delete this file immediately after use. Do not keep it on your server.
        </div>

        <div class="card">
            <div class="header">
                <h1>⚡ Laravel Cache Clear</h1>
                <span class="badge-success">Operation Complete</span>
            </div>

            <ul class="checklist">
                <li><span class="icon-check">✓</span> Application cache</li>
                <li><span class="icon-check">✓</span> Route cache</li>
                <li><span class="icon-check">✓</span> Config cache</li>
                <li><span class="icon-check">✓</span> View cache</li>
                <li><span class="icon-check">✓</span> Compiled files</li>
            </ul>

            <div class="output-box"><?php echo htmlspecialchars($outputContent, ENT_QUOTES, 'UTF-8'); ?></div>

            <div class="exit-code">
                Exit Code: <span style="color: <?php echo $exitCode === 0 ? '#34d399' : '#f87171'; ?>;"><?php echo (int)$exitCode; ?></span>
            </div>
        </div>
    </div>
</body>
</html>
