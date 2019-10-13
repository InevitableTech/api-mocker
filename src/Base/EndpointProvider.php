<?php

namespace Genesis\Api\Mocker\Base;

use Genesis\Api\Mocker\Contract\StorageHandler;
use Genesis\Api\Mocker\Service\Curl;
use Psr\Http\Message\ServerRequestInterface;

/**
 * EndpointProvider class.
 */
class EndpointProvider
{
    protected static $server;
    protected static $get;
    protected static $post;
    public static $rawInput;
    protected static $storageHandler;
    protected static $request;

    /**
     * @param string $server
     * @param string $get
     * @param string $post
     * @param mixed  $storageHandler
     * @param mixed  $rawInput
     */
    public function __construct(
        StorageHandler $storageHandler,
        $server,
        $get,
        $post,
        $rawInput,
        ServerRequestInterface $request
    ) {
        self::$storageHandler = $storageHandler;
        self::$server = $server;
        self::$get = $get;
        self::$post = $post;
        self::$rawInput = $rawInput;
        self::$request = $request;
    }

    public function responseType(): string
    {
        return JsonResponse::class;
    }

    public function validate()
    {
        return;
    }

    public function isMockingRequest(): bool
    {
        return isset(self::$rawInput['mockData']) && self::$request->getMethod() === 'POST';
    }

    public function isPurgeRequest(): bool
    {
        return isset(self::$get['purge']);
    }

    private static function namespace($path = null): string
    {
        return trim($path ?? self::$request->getUri()->getPath() ?? '/', '/');
    }

    public static function endpoint($path = null): string
    {
        return str_replace(['\\', '/'], '---', self::namespace($path));
    }

    public function get(): MethodResponse
    {
        return $this->getResponse('get');
    }

    public function post(): MethodResponse
    {
        return $this->getResponse('post');
    }

    public function delete(): MethodResponse
    {
        return $this->getResponse('delete');
    }

    public function put(): MethodResponse
    {
        return $this->getResponse('put');
    }

    public function options(): MethodResponse
    {
        return $this->getResponse('options') ?? new MethodResponse(
            null,
            [
                'Access-Control-Allow-Headers' => '*',
                'Access-Control-Allow-Origin' => '*',
            ],
            204
        );
    }

    /**
     * @return string
     */
    public function purge(): MethodResponse
    {
        return new MethodResponse([
            'msg' => self::$storageHandler->purge(),
            'status' => 200
        ]);
    }

    public static function hasEndpointMock(): bool
    {
        $endpoint = self::endpoint();

        try {
            self::$storageHandler->get($endpoint);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function getResponse($method): MethodResponse
    {
        $endpoint = self::endpoint();

        try {
            $storedResponse = self::$storageHandler->get($endpoint);
        } catch (\UnexpectedValueException $e) {
            throw $e;
        } catch (\Exception $e) {
            $storedResponse = [];
        }

        $response = new EndpointResponse($storedResponse);
        $methodResponse = $response->get(self::$request->getUri(), $method);
        $content = $response->getArray();

        if (! $methodResponse) {
            return new MethodResponse();
        }

        self::$storageHandler->save($endpoint, $content);

        if ($methodResponse instanceof MethodResponse) {
            return $methodResponse;
        }

        throw new Exception('Expected method response');
    }

    /**
     * {
     *    "url": "/arya/ports/abc123",
     *    "get": [{
     *        "body": {
     *            "UUID": "theportuuidgoeshere",
     *            "summary": "theportsummarygoeshere"
     *        }
     *    }]
     * }.
     *
     * @return string
     * @param  mixed  $response
     */
    public function consume(array $response): MethodResponse
    {
        if (!isset($response['url'])) {
            throw new \Exception('Url must be provided.');
        }

        $endpoint = self::endpoint($response['url']);

        try {
            $existingData = self::$storageHandler->get($endpoint);
        } catch (\Exception $e) {
            $existingData = [];
        }

        foreach ($response as $responseType => $responseContent) {
            if (!is_array($responseContent)) {
                continue;
            }

            $this->checkIntegrityOfResponses($responseContent);
            foreach ($responseContent as $index => $singleResponse) {
                if (isset($existingData[$responseType])) {
                    throw new AppException(sprintf(
                        'Mock for url and method type "%s" already exists. Purge first.',
                        $responseType
                    ));
                }

                if (!isset($singleResponse['with'])) {
                    $response[$responseType][$index]['with'] = null;
                } elseif (preg_match($singleResponse['with'], null) === false) {
                    throw new AppException("Regex pattern '{$singleResponse['with']}' is invalid.");
                }

                if (isset($singleResponse['multi_body'])) {
                    $response[$responseType][$index]['index'] = 0;
                }
            }

            // Preserve existing mocks.
            $existingData[$responseType] = $response[$responseType];
        }

        try {
            self::$storageHandler->save($endpoint, $existingData);
        } catch (\Exception $e) {
            return new MethodResponse([
                'status' => 500,
                'msg' => 'Unable to save mock to file.',
                'mock' => $existingData
            ], [], 500);
        }

        return new MethodResponse(['status' => 'OK', 'mock' => $response], [], 200);
    }

    private function checkIntegrityOfResponses(array $responses)
    {
        foreach ($responses as $index => $response) {
            if ($index === 0) {
                continue;
            }

            if (!isset($response['with'])) {
                throw new AppException('Each response after the first must include a with regex pattern to match on.');
            }
        }
    }

    public static function forward(string $domain, $body = null, array $headers = [])
    {
        return Curl::sendRequest(
            self::$request->getMethod(),
            $domain . self::$request->getUri(),
            $body,
            $headers
        );
    }

    public static function forwardDefault(string $url, array $headers = [])
    {
        $data = self::${self::$request->getMethod()} ?? [];

        return Curl::sendRequest(
            self::$request->getMethod(),
            $url,
            $data,
            array_merge(self::requestHeaders(), $headers)
        );
    }

    public static function requestHeaders()
    {
        $headers = array();
        foreach (self::$server as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_') {
                continue;
            }
            $headers[$key] = $value;
        }
        return $headers;
    }
}
