<?php

declare(strict_types=1);

namespace Lounisbou\CellLocation;

use Lounisbou\CellLocation\Services\CellLocationServiceInterface;
use Lounisbou\CellLocation\CellLocation;
use RuntimeException;

class CellLocator
{
    private CellLocationServiceInterface $locationService;

    public function __construct(CellLocationServiceInterface $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * Get CellLocation based on MCC, MNC, LAC, and CellID.
     * 
     * @param CellData $cellData Cell data.
     * @return CellLocation|null Cell location or null if not found.
     * @throws RuntimeException on API error
     */
    public function getLocation(CellData $cellData): ?CellLocation
    {
        try {
            $response = $this->locationService->getLocation($cellData);
            if ($response) {
                $location = new CellLocation(
                    $response['lat'], 
                    $response['lon'], 
                    $response['accuracy'], 
                    $response['address'] ?? null,
                );
                return $location;
            }
            return null;
        } catch (RuntimeException $e) {
            throw new RuntimeException('Geolocation service error: ' . $e->getMessage());
        }
    }

    /**
     * Get the location (latitude and longitude) based an array of cell data objects.
     * Multiple cell data objects can be used to improve the accuracy of the location.
     * Triangulation can be used to estimate the location based on multiple cell towers.
     * 
     * @param array<CellData> $cells Array of cell data objects.
     * @return array|null Location (latitude and longitude) or null if not found
     * @throws RuntimeException on API error
     */
    public function getTriangulatedLocation(array $cells): ?CellLocation
    {
        // Check if there is at least one cell data object
        if (empty($cells)) {
            throw new RuntimeException('At least one cell data object is required');
        }

        // Get the location for each cell data object
        $locations = [];
        foreach ($cells as $cell) {
            $location = $this->getLocation($cell);
            if ($location) {
                $locations[] = $location;
            }
        }

        // Check if there is at least one location found
        if (empty($locations)) {
            return null;
        }

        // Initialize sum variables for latitude, longitude, accuracy and weights 
        $weightedLatSum = 0;
        $weightedLonSum = 0;
        $accuracySum = 0;
        $weightSum = 0;


        // Calculate the average latitude and longitude
        foreach ($locations as $location) {

            // Calculate the weight as the inverse square of the accuracy (more accurate towers weigh more)
            $weight = 1 / ($location->accuracy ** 2);
            
            // Add weighted lat/lon
            $weightedLatSum += $location->latitude * $weight;
            $weightedLonSum += $location->longitude * $weight;

            // Add weighted accuracy
            $accuracySum += $location->accuracy * $weight;

            // Sum of weights
            $weightSum += $weight;
        }
        
        // Calculate the final location
        $finalLat = $weightedLatSum / $weightSum;
        $finalLon = $weightedLonSum / $weightSum;
        $finalAccuracy = $accuracySum / $weightSum;

        // Return the final location
        return new CellLocation($finalLat, $finalLon, $finalAccuracy);
    }

}
