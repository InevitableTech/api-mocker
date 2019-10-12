<?php

namespace Genesis\Api\Mocker\Base;

/**
 * JsonResponse class.
 */
class JsonResponse extends Response
{
    final public function transformBody($body): string
    {
        return !is_string($body) ? json_encode($body) : $body;
    }

    /**
     * @return array
     */
    public function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Access-Control-Allow-Headers' => '*',
            'Access-Control-Allow-Origin' => '*',
        ];
    }
}
