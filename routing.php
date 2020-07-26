<?php

use Genesis\Api\Mocker\Mocks;
use Genesis\Api\Mocker\StaticCaller;

return [
    '/mocks' => calls(Mocks::class),
    '*' => calls(StaticCaller::class),
];
