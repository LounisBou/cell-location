<?php

declare(strict_types=1);

namespace Lounisbou\CellLocation;

use Lounisbou\CellLocation\CellLocationServiceInterface;
use RuntimeException;

class OpenCellIDService implements CellLocationServiceInterface
{
    private const string API_URL = 'https://opencellid.org/cell/get';
    private string $apiKey;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Get the location (latitude and longitude) based on MCC, MNC, LAC, and CellID.
     *
     * @param int $mcc
     * @param int $mnc
     * @param int $lac
     * @param int $cellId
     * @param RadioType $radioType Radio type (GSM, CDMA, WCDMA, LTE)
     * @return array|null Returns array ['lat' => float, 'lng' => float] or null if not found.
     * @throws RuntimeException If an error occurs during the request.
     */
    public function getLocation(int $mcc, int $mnc, int $lac, int $cellId, RadioType $radioType = RadioType::GSM): ?array
    {
        // Build the full API URL
        $url = sprintf(
            '%s?key=%s&mcc=%d&mnc=%d&lac=%d&cellid=%d&radio=%s&format=xml',
            self::API_URL,
            $this->apiKey,
            $mcc,
            $mnc,
            $lac,
            $cellId,
            $radioType->value,
        );

        // Create the context options for the HTTP request
        $contextOptions = [
            'http' => [
                'method' => 'GET',
                'header' => 'Content-Type: application/xml',
                'timeout' => 10,  // Timeout for the request
            ],
        ];

        var_dump($url, $contextOptions);

        // Create the context resource
        $context = stream_context_create($contextOptions);

        // Perform the request
        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            throw new RuntimeException('Failed to connect to the OpenCellID API.');
        }

        // Parse the XML response
        $xml = simplexml_load_string($response);
        if ($xml === false || (string)$xml['stat'] !== 'ok') {
            throw new RuntimeException('Failed to retrieve valid response from OpenCellID.');
        }

        // Extract the latitude and longitude
        $lat = (string)$xml->cell['lat'];
        $lon = (string)$xml->cell['lon'];

        if ($lat && $lon) {
            return [
                'lat' => (float)$lat,
                'lon' => (float)$lon,
            ];
        }

        return null;
    }
}
