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
     * @return CellLocation|null Location (latitude and longitude) or null if not found
     * @throws RuntimeException on API error
     */
    public function getLocation(CellData $cellData): ?CellLocation
    {
        try {
            $response = $this->locationService->getLocation($cellData);
            if ($response) {
                $location = new CellLocation($response['lat'], $response['lon'], $response['accuracy'] ?? null);
                return $location;
            }
            return null;
        } catch (RuntimeException $e) {
            throw new RuntimeException('Geolocation service error: ' . $e->getMessage());
        }
    }
}
