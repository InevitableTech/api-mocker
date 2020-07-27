<?php

namespace Genesis\Api\Mocker;

use DirectoryIterator;
use Genesis\Api\Mocker\Base\EndpointProvider;
use Genesis\Api\Mocker\Base\EndpointResponse;
use Genesis\Api\Mocker\Base\MethodResponse;

/**
 * Static class.
 */
class StaticCaller extends EndpointProvider
{
    private static $size = 0;

    private static $staticCalls = [];

    /**
     * This is wrong, statics need to stay in place.
     *
     * @return string
     */
    public function warmUp(): StaticCaller
    {
        error_log('Warming up statics...');

        $directory = new DirectoryIterator(getenv('API_MOCK_STATICS_DIR'));
        $size = $directory->getSize();

        if (self::$size === $size) {
            return $this;
        }

        foreach ($directory as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $mockDefinition = json_decode(file_get_contents($fileinfo->getPathname()), true);
                error_log('Loading ' . $mockDefinition['mockData']['url']);
                self::$staticCalls[$mockDefinition['mockData']['url']] = $mockDefinition['mockData'];
                error_log('Ready ' . $mockDefinition['mockData']['url']);
            }
        }
        self::$size = $size;

        return $this;
    }

    public function getResponse($method): MethodResponse
    {
        if (!isset(self::$staticCalls[self::$request->getUri()->getPath()][$method])) {
            return new MethodResponse();
        }

        $response = new EndpointResponse(self::$staticCalls[self::$request->getUri()->getPath()]);

        return $response->get(self::$request->getUri(), $method);
    }
}
