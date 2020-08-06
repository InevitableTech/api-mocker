<?php

namespace Genesis\Api\Mocker\Base;

use Genesis\Api\Mocker\StaticCaller;
use React\Http\Response;

/**
 * Dispatcher class.
 */
class Dispatcher
{
    /**
     * @param EndpointProvider $controller
     * @param string           $method
     */
    public static function dispatch(EndpointProvider $controller, string $method): Response
    {
        $responseClass = $controller->responseType();

        $methodResponse = null;
        if ($controller->isPurgeRequest()) {
            error_log('received purge request...');
            $methodResponse = $controller->purgeMocks();
            error_log('purged.');
        } elseif ($controller->isMockingRequest()) {
            error_log('received mock request...');
            $controller->validate();
            error_log('mocked: ' . $method);
            $methodResponse = $controller->consume($controller::$rawInput['mockData']);
        } elseif ($controller instanceof StaticCaller) {
            error_log('retrieving static response');
            $methodResponse = $controller->warmUp()->$method();
        } else {
            error_log('received response request...');
            $methodResponse = $controller->$method();
            error_log('returning response...');
        }

        if (!($methodResponse instanceof MethodResponse)) {
            throw new \Exception('Response must be of type MethodResponse.');
        }

        $response = new $responseClass($methodResponse);

        return new Response(
            $response->responseCode,
            $response->headers,
            $response->body
        );
    }
}