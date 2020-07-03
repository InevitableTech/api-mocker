<?php

namespace Genesis\Api\Mocker\Base;

/**
 * MultiResponse class.
 */
class MultiResponse
{
    public function __construct($response)
    {
        if (! isset($response['consecutive_responses'])) {
            throw new \Exception('Data must contain multi body index for MultiResponse: ' .
                print_r($response, true));
        }

        $this->response = $response;
    }

    public function get($index): ?MethodResponse
    {
        return $this->response['consecutive_responses'][$index];
    }

    public function getNext(): ?MethodResponse
    {
        $index = $this->response['index'];

        if (! isset($this->response['consecutive_responses'][$index])) {
            return null;
        }

        $response = $this->response['consecutive_responses'][$index];
        $this->response['index'] += 1;

        return $response;
    }

    public function getArray(): array
    {
        $responseContent = [];

        foreach ($this->response['consecutive_responses'] as $response) {
            $responseContent['consecutive_responses'][] = $response->getArray();
        }

        $responseContent['index'] = $this->response['index'];
        $responseContent['with'] = $this->response['with'];

        return $responseContent;
    }

    public function getWith(): ?string
    {
        return $this->response['with'];
    }
}
