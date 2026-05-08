<?php

/**
 * Simple deploy webhook for CHOMIN
 * Trigger: GET/POST https://your-domain.com/deploy.php?token=YOUR_TOKEN
 *
 * This runs the production deploy steps when called with the correct token.
 */

$secret = getenv('DEPLOY_TOKEN');

if (!$secret) {
    http_response_code(503);
    die('DEPLOY_TOKEN is not configured');
}

$token = $_GET['token'] ?? $_POST['token'] ?? '';

if (!hash_equals($secret, $token)) {
    http_response_code(403);
    die('Forbidden');
}

header('Content-Type: text/plain; charset=utf-8');

$projectDir = __DIR__;

$commands = [
    "cd $projectDir && git pull origin main 2>&1",
    "cd $projectDir && composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader 2>&1",
    "cd $projectDir && php artisan migrate --force 2>&1",
    "cd $projectDir && php artisan storage:link 2>&1",
    "cd $projectDir && php artisan optimize:clear 2>&1",
    "cd $projectDir && php artisan config:cache 2>&1",
    "cd $projectDir && php artisan route:cache 2>&1",
    "cd $projectDir && php artisan view:cache 2>&1",
];

echo "=== CHOMIN Deploy ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

foreach ($commands as $cmd) {
    echo "$ $cmd\n";
    $output = shell_exec($cmd);
    echo $output . "\n";
}

echo "=== Deploy Complete ===\n";
