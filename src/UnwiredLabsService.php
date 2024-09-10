<?php

declare(strict_types=1);

namespace Lounisbou\CellLocation;

use RuntimeException;

class UnwiredLabsService implements CellLocationServiceInterface
{
    // UnwiredLabs API URL
    private const API_URL = 'https://us1.unwiredlabs.com/v2/process.php';

    // API key
    private string $apiKey;

    /**
     * Constructor to initialize the API key.
     * 
     * @param string $apiKey
     */
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Get the location (latitude and longitude) based on MCC, MNC, LAC, and CellID.
     * 
     * @param int $mcc Mobile Country Code
     * @param int $mnc Mobile Network Code
     * @param int $lac Location Area Code
     * @param int $cellId Cell ID
     * @param RadioType $radioType Radio type (GSM, CDMA, WCDMA, LTE)
     * @return array|null Location (latitude and longitude) or null if not found
     * @throws RuntimeException on cURL or API error
     */
    public function getLocation(int $mcc, int $mnc, int $lac, int $cellId, RadioType $radioType = RadioType::GSM): ?array
    {
        // Data payload to send to UnwiredLabs API
        $data = [
            'token' => $this->apiKey,
            'radio' => $radioType->value, 
            'mcc' => $mcc,
            'mnc' => $mnc,
            'cells' => [
                [
                    'lac' => $lac,
                    'cid' => $cellId,
                ],
            ],
            'address' => 1,
        ];

        // Execute the HTTP request and handle the response
        $response = $this->executeRequest(self::API_URL, $data);

        // Validate and extract the location data from the response
        if (isset($response['lat']) && isset($response['lon'])) {
            return [
                'lat' => (float) $response['lat'],
                'lon' => (float) $response['lon'],
            ];
        }

        // Return null if no valid location data is found
        return null;
    }

    /**
     * Executes the cURL request to the UnwiredLabs API.
     * 
     * @param string $url API URL
     * @param array $data Payload to send to the API
     * @return array Decoded response as an associative array
     * @throws RuntimeException on cURL or API error
     */
    private function executeRequest(string $url, array $data): array
    {
        $curlHandle = curl_init($url);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($curlHandle, CURLOPT_POST, true);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, json_encode($data));

        // Execute the request
        $response = curl_exec($curlHandle);

        // Handle any cURL errors
        if ($response === false || curl_errno($curlHandle)) {
            $error = curl_error($curlHandle);
            curl_close($curlHandle);
            throw new RuntimeException('cURL Error: ' . $error);
        }

        curl_close($curlHandle);

        // Decode the JSON response
        $decodedResponse = json_decode($response, true);

        // Handle JSON decoding errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Invalid JSON response from UnwiredLabs API: ' . json_last_error_msg());
        }

        return $decodedResponse;
    }
}
