<?php

// The autoloader config needs to be set when used as a package.
require_once __DIR__ . '/vendor/autoload.php';

use Genesis\Api\Mocker\Base\AppException;
use Genesis\Api\Mocker\Base\Dispatcher;
use Genesis\Api\Mocker\Base\FileStorage;
use Genesis\Api\Mocker\Base\Router;
use Psr\Http\Message\ServerRequestInterface;

function calls($controller)
{
    return [
        'controller' => $controller
    ];
}

$loop = React\EventLoop\Factory::create();

$server = new React\Http\Server(function (ServerRequestInterface $request) {
    try {
        $raw = (string) $request->getBody();
        $server = $request->getServerParams();

        $rawInput = '';
        if ($raw) {
            $rawInput = json_decode($raw, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new AppException('Invalid json provided: ' . json_last_error_msg());
            }
        }
        $path = $request->getUri()->getPath() ?? '/';

        // Get the router to point to a controller.
        $routing = include __DIR__ . '/routing.php';
        $controllerClass = Router::getControllerForUrl($path, $routing, $request);

        try {
            $controller = new $controllerClass(
                new FileStorage(),
                $server,
                $request->getQueryParams(),
                $request->getParsedBody(),
                $rawInput,
                $request
            );
        } catch (\Exception $e) {
            throw new AppException($e->getMessage());
        }

        return Dispatcher::dispatch(
            $controller,
            strtolower($request->getMethod())
        );
    } catch (Exception $e) {
        throw new AppException($e->getMessage());
    }
});

// Make this configurable.
$port = 8989;
$socket = new React\Socket\Server($port, $loop);
$server->listen($socket);

$server->on('error', function (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
    if ($e->getPrevious() !== null) {
        $previousException = $e->getPrevious();
        echo $previousException->getMessage() . PHP_EOL;
    }
});

echo 'Listening on: http://localhost:' . $port . PHP_EOL;
$loop->run();
