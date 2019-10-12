<?php

namespace Genesis\Api\Mocker;

use Genesis\Api\Mocker\Base\EndpointProvider;
use Genesis\Api\Mocker\Base\MethodResponse;

class Mocks extends EndpointProvider
{
    public function get(): MethodResponse
    {
        $index = self::$storageHandler->getIndex();

        $mocks = [];
        foreach ($index as $file) {
            $mocks[$file] = file_get_contents($file);
        }

        return new MethodResponse(
            $mocks
        );
    }
}
