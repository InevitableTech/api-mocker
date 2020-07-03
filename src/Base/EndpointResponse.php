<?php

namespace Genesis\Api\Mocker\Base;

/**
 * EndpointResponse class.
 */
class EndpointResponse
{
    private $rawResponse;

    private $response = null;

    private $method;

    public function __construct(array $response = null)
    {
        $this->rawResponse = $response;
        $this->url = $response['url'] ?? null;
        $this->response = [
            'get' => isset($response['get']) ? $this->getResponseContent($response['get']) : null,
            'post' => isset($response['post']) ? $this->getResponseContent($response['post']) : null,
            'delete' => isset($response['delete']) ? $this->getResponseContent($response['delete']) : null,
            'put' => isset($response['put']) ? $this->getResponseContent($response['put']) : null,
            'options' => isset($response['options']) ? $this->getResponseContent($response['options']) : null,
        ];
    }

    private function getResponseContent($responses)
    {
        $prepped = [];
        foreach ($responses as $response) {
            if (isset($response['consecutive_responses'])) {
                $responseContent = [
                    'index' => $response['index'] ?? null,
                    'with' => $response['with'] ?? null,
                    'consecutive_responses' => []
                ];
                foreach ($response['consecutive_responses'] as $index => $singleBody) {
                    $responseContent['consecutive_responses'][] = new MethodResponse(
                        $singleBody['body'] ?? null,
                        $singleBody['headers'] ?? [],
                        $singleBody['response_code'] ?? null,
                        $response['with'] ?? null,
                        $singleBody['proxy'] ?? []
                    );

                    if ($responseContent['index'] === null) {
                        $responseContent['index'] = 0;
                    }
                }

                $prepped[] = new MultiResponse($responseContent);
            } else {
                $prepped[] = new MethodResponse(
                    $response['body'] ?? null,
                    $response['headers'] ?? [],
                    $response['response_code'] ?? null,
                    $response['with'] ?? null,
                    $response['proxy'] ?? []
                );
            }
        }

        return $prepped;
    }

    public function isMulti($method)
    {
        return $this->response[$method] instanceof MultiResponse;
    }

    public function getArray(): array
    {
        $responseContent = [];

        foreach ($this->response as $method => $responses) {
            if (! is_array($responses)) {
                continue;
            }
            foreach ($responses as $index => $response) {
                $responseContent[$method][$index] = $response ? $response->getArray() : null;
            }
        }

        return $responseContent;
    }

    public function getRawResponse()
    {
        return $this->rawResponse;
    }

    public function get($url, $method): ?MethodResponse
    {
        if (!is_array($this->response[$method])) {
            return null;
        }

        $successor = null;
        foreach ($this->response[$method] as $response) {
            $with = $response->getWith();
            if ($with) {
                $result = preg_match($with, $url);
                if ($result === 1) {
                    $successor = $response;
                } elseif ($result === false) {
                    throw new AppException("Regex pattern '$with' is invalid.");
                }
            } else {
                $successor = $response;
            }
        }

        $methodResponse = null;
        if ($successor instanceof MultiResponse) {
            $methodResponse = $successor->getNext();
        } else {
            $methodResponse = $successor;
        }

        if ($methodResponse && $proxy = $methodResponse->getProxy()) {
            error_log('proxying channel: ' . $proxy['url']);
            try {
                $methodResponse = new MethodResponse(
                    EndpointProvider::forwardDefault($proxy['url'], $proxy['headers']),
                    $methodResponse->getHeaders(),
                    $methodResponse->getResponseCode()
                );
            } catch (\Exception $e) {
                error_log('Exception: ' . $e->getMessage());
            }
        }

        return $methodResponse;
    }
}
