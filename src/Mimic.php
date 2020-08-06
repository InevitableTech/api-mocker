<?php

namespace Genesis\Api\Mocker;

use DirectoryIterator;
use Genesis\Api\Mocker\Base\EndpointProvider;
use Genesis\Api\Mocker\Base\MethodResponse;

class Mimic extends EndpointProvider
{
    /**
     * POST mock request to mimic another request. This can be static or dynamic.
     *
     * {
     *     "mockType": "standard|mimic:dynamic|mimic:standard"
     *     "mockData": {
     *          "url": "/alive",
                "get": [{
                    "proxy": {
                        "url": "http://google.com"
                    }
                }]
     *     }
     * }
     *
     * @return MethodResponse
     */
    public function post(): MethodResponse
    {

    }
}
