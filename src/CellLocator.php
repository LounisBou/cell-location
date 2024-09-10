<?php

declare(strict_types=1);

namespace CellLocation;

use CellLocation\CellLocationServiceInterface;
use RuntimeException;

class CellLocator
{
    private CellLocationServiceInterface $locationService;

    public function __construct(CellLocationServiceInterface $locationService)
    {
        $this->locationService = $locationService;
    }

    public function findLocation(int $mcc, int $mnc, int $lac, int $cellId): void
    {
        try {
            $position = $this->locationService->getLocation($mcc, $mnc, $lac, $cellId);

            if ($position) {
                echo "Latitude: " . $position['lat'] . ", Longitude: " . $position['lng'] . PHP_EOL;
            } else {
                echo "Location not found." . PHP_EOL;
            }
        } catch (RuntimeException $e) {
            // Log the exception or handle it appropriately
            echo "Error: " . $e->getMessage() . PHP_EOL;
        }
    }
}
