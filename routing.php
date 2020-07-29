<?php

use Genesis\Api\Mocker\Mocks;
use Genesis\Api\Mocker\StaticCaller;

$routes = [];
$file = './routes/routing.php';
if (file_exists($file)) {
    $routes = require $file;
    if (!is_array($routes)) {
        throw new Exception('The routes file defined should return an array.');
    }
}

return array_merge([
    '/mocks' => calls(Mocks::class),
    '*' => calls(StaticCaller::class),
], $routes);
