<?php

declare(strict_types=1);

namespace Lounisbou\CellLocation\Services;

use Lounisbou\CellLocation\CellData;
use Lounisbou\CellLocation\Services\CellLocationServiceInterface;
use Lounisbou\CellLocation\RadioType;
use RuntimeException;

class GoogleGeolocationService implements CellLocationServiceInterface
{
    // Google Geolocation API URL
    private const API_URL = 'https://www.googleapis.com/geolocation/v1/geolocate?key=';

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
     * @return array|null Location (latitude and longitude) or null if the location is not found
     * @throws RuntimeException on cURL or API error
     */
    public function getLocation(CellData $cellData): ?array
    {
        // Prepare the request data to send to Google Geolocation API
        $data = [
            'homeMobileCountryCode' => $cellData->mcc,
            'homeMobileNetworkCode' => $cellData->mnc,
            'radioType' => $cellData->radioType->value,
            'cellTowers' => [
                [
                    'cellId' => $cellData->cellId,
                    'locationAreaCode' => $cellData->lac,
                    'mobileCountryCode' => $cellData->mcc,
                    'mobileNetworkCode' => $cellData->mnc
                ]
            ]
        ];

        // Execute the HTTP request and handle the response
        $response = $this->executeRequest(self::API_URL . $this->apiKey, $data);

        // Validate and extract the location data from the response
        if (isset($response['location']['lat']) && isset($response['location']['lng'])) {
            return [
                'lat' => (float)$response['location']['lat'],
                'lon' => (float)$response['location']['lng'],
                'accuracy' =>(float)$response['accuracy'] ?? null
            ];
        }

        // Return null if no valid location data is found
        return null;
    }

    /**
     * Executes the cURL request to the Google Geolocation API.
     * 
     * @param string $url API URL
     * @param array $data Payload to send to the API
     * @return array Decoded response as an associative array
     * @throws RuntimeException on cURL or API error
     */
    private function executeRequest(string $url, array $data): array
    {
        // Initialize cURL
        $curlHandle = curl_init($url);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($curlHandle, CURLOPT_POST, true);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, json_encode($data));

        // Execute the request
        $response = curl_exec($curlHandle);

        // Convert the response to an associative array
        $response = json_decode($response, true);
            
        // Handle any cURL errors
        if ($response === false || curl_errno($curlHandle)) {
            $error = curl_error($curlHandle);
            curl_close($curlHandle);
            throw new RuntimeException('cURL Error: ' . $error);
        }
        curl_close($curlHandle);

        // Check API response for errors
        if (isset($response['error'])) {
            $error = $response['error'];
            $message = $error['message'] ?? 'Unknown error';
            $code = $error['code'] ?? 'Unknown code';
            throw new RuntimeException('Code ' . $code . ' - ' . $message);
        }

        // Handle JSON decoding errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Invalid JSON response from Google API: ' . json_last_error_msg());
        }

        // Return the location as float values
        return $response;
    }
}
