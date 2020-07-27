<?php

namespace Genesis\Api\Mocker;

use DirectoryIterator;
use Genesis\Api\Mocker\Base\EndpointProvider;
use Genesis\Api\Mocker\Base\MethodResponse;

class Mocks extends EndpointProvider
{
    public function get(): MethodResponse
    {
        // Static configuration
        $static = [];
        $directory = new DirectoryIterator(getenv('API_MOCK_STATICS_DIR'));
        foreach ($directory as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $static[$fileinfo->getFilename()] = file_get_contents($fileinfo->getPathname());
            }
        }

        $index = self::$storageHandler->getIndex();

        $dynamic = [];
        foreach ($index as $file) {
            $dynamic[$file] = file_get_contents($file);
        }

        return new MethodResponse(
            [
                'static' => $static,
                'dynamic' => $dynamic
            ]
        );
    }
}
