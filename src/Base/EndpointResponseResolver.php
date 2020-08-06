<?php

namespace Genesis\Api\Mocker\Base;

use Exception;
use Genesis\Api\Mocker\Base\AppException;
use Genesis\Api\Mocker\Base\EndpointProvider;
use Genesis\Api\Mocker\Base\MethodResponse;

/**
 * EndpointResponseResolver class.
 */
class EndpointResponseResolver
{
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
     * @param array $response
     * @param array $existingData
     *
     * @return array
     */
    public static function resolveData(array $response, array $existingData): array
    {
        foreach ($response as $responseType => $responseContent) {
            // All response types contain an array.
            if (!is_array($responseContent)) {
                continue;
            }

            self::checkIntegrityOfResponses($responseContent);
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

                if (isset($singleResponse['consecutive_responses'])) {
                    $response[$responseType][$index]['index'] = 0;
                }
            }

            // Preserve existing mocks.
            $existingData[$responseType] = $response[$responseType];
        }

        return $existingData;
    }

    private static function checkIntegrityOfResponses(array $responses)
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
}
