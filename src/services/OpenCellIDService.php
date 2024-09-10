<?php

declare(strict_types=1);

namespace Lounisbou\CellLocation\Services;

use Lounisbou\CellLocation\CellData;
use Lounisbou\CellLocation\Services\CellLocationServiceInterface;
use Lounisbou\CellLocation\RadioType;
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
     * @param CellData $cellData Cell data.
     * @return array|null Returns array ['lat' => float, 'lng' => float] or null if not found.
     * @throws RuntimeException If an error occurs during the request.
     */
    public function getLocation(CellData $cellData): ?array
    {
        // Build the full API URL
        $url = sprintf(
            '%s?key=%s&mcc=%d&mnc=%d&lac=%d&cellid=%d&radio=%s&format=xml',
            self::API_URL,
            $this->apiKey,
            $cellData->mcc,
            $cellData->mnc,
            $cellData->lac,
            $cellData->cellId,
            $cellData->radioType->value,
        );

        // Create the context options for the HTTP request
        $contextOptions = [
            'http' => [
                'method' => 'GET',
                'header' => 'Content-Type: application/xml',
                'timeout' => 10,  // Timeout for the request
            ],
        ];

        // Create the context resource
        $context = stream_context_create($contextOptions);

        // Perform the request
        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            throw new RuntimeException('Failed to connect to the OpenCellID API');
        }

        // Parse the XML response
        $xml = simplexml_load_string($response);
        if ($xml === false || (string)$xml['stat'] !== 'ok') {
            // Check if error code is 1 (no result found)
            if ((string)$xml->err['code'] === '1') {
                return null;
            }
            throw new RuntimeException('Code ' . (string)$xml->err['code'] . ' - ' . (string)$xml->err['info']);
        }

        // Check if lat and lon are present in the response
        if (!isset($xml->cell['lat']) || !isset($xml->cell['lon'])) {
            throw new RuntimeException('Invalid response from OpenCellID API: missing lat or lon');
        }
        
        // Extract the latitude and longitude
        $lat = (string)$xml->cell['lat'];
        $lon = (string)$xml->cell['lon'];

        // Return the location as float values
        return [
            'lat' => (float)$lat,
            'lon' => (float)$lon,
        ];
    }
}
