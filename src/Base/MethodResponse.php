<?php

namespace Genesis\Api\Mocker\Base;

/**
 * MethodResponse class.
 */
class MethodResponse
{
    private $body;
    private $headers = [];
    private $statusCode = '';
    private $with;
    private $proxy = [];

    public function __construct($body = null, array $headers = [], int $statusCode = null, string $with = null, array $proxy = [])
    {
        $this->body = $body;
        $this->headers = $headers;
        $this->statusCode = $statusCode;
        $this->with = $with;

        if ($proxy) {
            if (!isset($proxy['url'])) {
                throw new \Exception('Proxy must be provided with a url.');
            }

            $this->proxy = [
                'url' => $proxy['url'],
                'headers' => $proxy['headers'] ?? []
            ];
        }
    }

    public function getResponse(): array
    {
        return [
            'body' => $this->body,
            'headers' => $this->headers,
            'response_code' => $this->statusCode
        ];
    }

    public function getArray(): array
    {
        return [
            'body' => $this->body,
            'headers' => $this->headers,
            'response_code' => $this->statusCode,
            'with' => $this->with,
            'proxy' => $this->proxy,
        ];
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getResponseCode(): ?int
    {
        return $this->statusCode;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getProxy(): array
    {
        return $this->proxy;
    }

    public function getWith(): ?string
    {
        return $this->with;
    }
}
