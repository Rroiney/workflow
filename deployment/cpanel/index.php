<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// cPanel serves this folder directly, while the full Laravel app lives in
// the sibling workflow-app directory.
if (file_exists($maintenance = __DIR__.'/../workflow-app/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../workflow-app/vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../workflow-app/bootstrap/app.php';

$app->handleRequest(Request::capture());
