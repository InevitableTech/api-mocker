<?php

namespace Genesis\Api\Mocker\Service;

/**
 * Curl class.
 */
class Curl
{
    public static function sendRequest($method, $url, $body = null, array $headers = [])
    {
        if (!$headers) {
            $headers['Content-Type'] = 'application/json';
        }

        $requestHeaders = [];
        foreach ($headers as $header => $value) {
            $requestHeaders[] = $header . ': ' . $value;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($body) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        $body = substr($result, $header_size);
        $response = json_decode($body);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception(
                'Unable to decode response, error: ' .
                json_last_error_msg() .
                print_r($body, true)
            );
        }

        return $response;
    }
}