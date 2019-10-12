<?php

namespace Genesis\Api\Mocker\Base;

/**
 * MultiResponse class.
 */
class MultiResponse
{
    public function __construct($response)
    {
        if (! isset($response['multi_body'])) {
            throw new \Exception('Data must contain multi body index for MultiResponse: ' .
                print_r($response, true));
        }

        $this->response = $response;
    }

    public function get($index): ?MethodResponse
    {
        return $this->response['multi_body'][$index];
    }

    public function getNext(): ?MethodResponse
    {
        $index = $this->response['index'];

        if (! isset($this->response['multi_body'][$index])) {
            return null;
        }

        $response = $this->response['multi_body'][$index];
        $this->response['index'] += 1;

        return $response;
    }

    public function getArray(): array
    {
        $responseContent = [];

        foreach ($this->response['multi_body'] as $response) {
            $responseContent['multi_body'][] = $response->getArray();
        }

        $responseContent['index'] = $this->response['index'];
        $responseContent['with'] = $this->response['with'];

        return $responseContent;
    }

    public function getWith(): string
    {
        return $this->response['with'];
    }
}
