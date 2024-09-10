<?php

declare(strict_types=1);

namespace Lounisbou\CellLocation;

use Lounisbou\CellLocation\CellLocationServiceInterface;
use Lounisbou\CellLocation\CellLocation;
use Lounisbou\CellLocation\RadioType;
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
     * @param int $mcc Mobile Country Code
     * @param int $mnc Mobile Network Code
     * @param int $lac Location Area Code
     * @param int $cellId Cell ID
     * @param RadioType $radioType Radio type (GSM, CDMA, WCDMA, LTE)
     * @return CellLocation|null Location (latitude and longitude) or null if not found
     * @throws RuntimeException on API error
     */
    public function getLocation(int $mcc, int $mnc, int $lac, int $cellId, RadioType $radioType = RadioType::GSM): ?CellLocation
    {
        try {
            $response = $this->locationService->getLocation($mcc, $mnc, $lac, $cellId);
            if ($response) {
                $location = new CellLocation($response['lat'], $response['lon']);
                return $location;
            }
            return null;
        } catch (RuntimeException $e) {
            throw new RuntimeException('Error: Failed to connect to the CellLocation API.');
        }
    }
}
