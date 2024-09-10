<?php

declare(strict_types=1);

namespace Lounisbou\CellLocation\Services;

use Lounisbou\CellLocation\CellData;
use RuntimeException;

/**
 * Interface for cell location services.
 */
interface CellLocationServiceInterface {
    /**
     * Get the location (latitude and longitude) based on MCC, MNC, LAC, and CellID.
     *
     * @param CellData $cellData Cell data.
     * @return array|null Returns array ['lat' => float, 'lon' => float] or null if not found.
     * @throws RuntimeException If an error occurs during the request.
     */
    public function getLocation(CellData $cellData): ?array;
}
