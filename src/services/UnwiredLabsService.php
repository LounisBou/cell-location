<?php

declare(strict_types=1);

namespace Lounisbou\CellLocation\Services;

use Lounisbou\CellLocation\CellData;
use Lounisbou\CellLocation\Services\CellLocationServiceInterface;
use Lounisbou\CellLocation\RadioType;

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
     * @param CellData $cellData Cell data.
     * @return array|null Location (latitude and longitude) or null if not found
     * @throws RuntimeException on cURL or API error
     */
    public function getLocation(CellData $cellData): ?array
    {
        // Data payload to send to UnwiredLabs API
        $data = [
            'token' => $this->apiKey,
            'radio' => $cellData->radioType->value, 
            'mcc' => $cellData->mcc,
            'mnc' => $cellData->mnc,
            'cells' => [
                [
                    'lac' => $cellData->lac,
                    'cid' => $cellData->cellId,
                ],
            ],
            'address' => 1,
        ];

        // Execute the HTTP request and handle the response
        $response = $this->executeRequest(self::API_URL, $data);

        // Check if response status is OK
        if (!isset($response['status']) || $response['status'] !== 'ok') {
            // Check if error is "No matches found" 
            if (isset($response['message']) && $response['message'] === 'No matches found') {
                return null;
            }
            throw new RuntimeException($response['message'] ?? 'Unknown error');
        }

        // Check if response contains lat and lon
        if (!isset($response['lat']) || !isset($response['lon'])) {
            throw new RuntimeException('Invalid response from UnwiredLabs API: missing lat or lon');
        }

        return [
            'lat' => (float) $response['lat'],
            'lon' => (float) $response['lon'],
        ];
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
