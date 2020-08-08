<?php

namespace Genesis\Api\Mocker\Representations;

/**
 * To create a mock request.
 */
class Mock
{
    private $url;

    private $method;

    private $with;

    private $responses;

    public function __construct($url, $method, $with, array $responses = [])
    {
        $this->url = $url;
        $this->method = $method;
        $this->with = $with;
        $this->responses = [];
        $this->setResponses($responses);
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getWith()
    {
        return $this->with;
    }

    public function getResponses()
    {
        return $this->responses;
    }

    public function addResponse(array $response)
    {
        $this->responses[] = [
            'code' => $mock['responseCode'],
            'headers' => $mock['responseHeaders'],
            'body' => $mock['responseBody'],
        ];

        return $this;
    }

    public function setResponses(array $responses)
    {
        $this->responses = [];

        foreach ($responses as $response) {
            $this->responses[] = [
                'code' => $mock['responseCode'],
                'headers' => $mock['responseHeaders'],
                'body' => $mock['responseBody'],
            ];
        }

        return $this;
    }
}
