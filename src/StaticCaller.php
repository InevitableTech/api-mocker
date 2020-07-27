<?php

namespace Genesis\Api\Mocker;

use DirectoryIterator;
use Genesis\Api\Mocker\Base\EndpointProvider;

/**
 * Static class.
 */
class StaticCaller extends EndpointProvider
{
    /**
     * This is wrong, statics need to stay in place.
     *
     * @return string
     */
    public function warmUp(): StaticCaller
    {
        error_log('Warming up statics...');

        $directory = new DirectoryIterator(getenv('API_MOCK_STATICS_DIR'));
        foreach ($directory as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $mockDefinition = json_decode(file_get_contents($fileinfo->getPathname()), true);
                error_log('Consuming ' . $mockDefinition['mockData']['url']);
                $this->consume($mockDefinition['mockData']);
                error_log('Ready ' . $mockDefinition['mockData']['url']);
            }
        }

        return $this;
    }
}
