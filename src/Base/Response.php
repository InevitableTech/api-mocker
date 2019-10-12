<?php

namespace Genesis\Api\Mocker\Base;

/**
 * Response class.
 */
class Response
{
    public $headers = [];
    public $body = '';
    public $responseCode = 200;

    public function __construct(MethodResponse $response)
    {
        $this->headers = array_merge($this->defaultHeaders(), $response->getHeaders());
        $this->body = sprintf($this->getTemplate(), $this->transformBody($response->getBody()));
        $this->responseCode = $response->getResponseCode() ?? 200;
    }

    protected function transformBody($body)
    {
        return $body;
    }

    /**
     * @return array
     */
    protected function defaultHeaders(): array
    {
        return [
            'Access-Control-Allow-Headers' => '*',
            'Access-Control-Allow-Origin' => '*',
        ];
    }

    /**
     * @return string
     */
    protected function getTemplate(): string
    {
        return '%s';
    }
}
