<?php

declare(strict_types=1);

namespace Lounisbou\CellLocation;

use Lounisbou\CellLocation\Services\CellLocationServiceInterface;
use PHPDistance\Point;
use PHPDistance\Route;
use PHPDistance\HaversineCalculator;
use PHPDistance\Enums\EarthRadius;
use RuntimeException;

/**
 * Class CellLocator
 *
 * This class provides methods to locate a device based on cell tower data
 * by performing multilateration (triangulation) using the positions and
 * accuracies of multiple cell towers.
 */
class CellLocator
{


    /** Defines precision constants for rounding. */

    /**
     * @var int The number of decimal places for coordinate precision (degrees).
     */
    const COORDINATE_PRECISION = 7;
    /**
     * @var int The number of decimal places for accuracy precision (meters).
     */
    const ACCURACY_PRECISION = 2;

    /**
     * @var CellLocationServiceInterface The service used to retrieve cell tower locations.
     */
    private CellLocationServiceInterface $locationService;

    /**
     * @var HaversineCalculator Calculator for distance calculations.
     */
    private HaversineCalculator $calculator;

    /**
     * Constructor for CellLocator.
     *
     * @param CellLocationServiceInterface $locationService The service used to get cell tower locations.
     */
    public function __construct(CellLocationServiceInterface $locationService)
    {
        $this->locationService = $locationService;
        $this->calculator = new HaversineCalculator(EarthRadius::MEAN);
    }

    /**
     * Get the location of a single cell tower.
     *
     * @param CellData $cellData Cell tower data.
     * @return CellLocation|null Cell location or null if not found.
     * @throws RuntimeException On API error.
     */
    public function getLocation(CellData $cellData): ?CellLocation
    {
        try {
            $response = $this->locationService->getLocation($cellData);
            if ($response) {
                $location = new CellLocation(
                    latitude: $response['lat'],
                    longitude: $response['lon'],
                    accuracy: $response['accuracy'],
                    address: $response['address'] ?? null,
                );
                return $location;
            }
            return null;
        } catch (RuntimeException $e) {
            throw new RuntimeException('Geolocation service error: ' . $e->getMessage());
        }
    }

    /**
     * Calculates the weighted midpoint between two CellLocation objects based on their accuracies.
     *
     * This method computes the weighted average of the positions of two CellLocation objects,
     * where the weights are inversely proportional to their accuracies (smaller accuracy values indicate higher precision).
     *
     * @param CellLocation $location1 The first CellLocation object.
     * @param CellLocation $location2 The second CellLocation object.
     * @return CellLocation The estimated CellLocation representing the weighted midpoint.
     * @throws RuntimeException If accuracy values are not positive.
     */
    private function calculateWeightedMidpoint(CellLocation $location1, CellLocation $location2): CellLocation
    {
        // Extract accuracies
        $accuracy1 = $location1->accuracy;
        $accuracy2 = $location2->accuracy;

        // Ensure accuracies are positive
        if ($accuracy1 <= 0 || $accuracy2 <= 0) {
            throw new RuntimeException('Accuracy values must be positive.');
        }

        // Use inverse of accuracy as weights (higher accuracy gets higher weight)
        $weight1 = 1 / $accuracy1;
        $weight2 = 1 / $accuracy2;

        $totalWeight = $weight1 + $weight2;

        // Calculate weighted average of latitudes and longitudes
        $latitude = ($weight1 * $location1->latitude + $weight2 * $location2->latitude) / $totalWeight;
        $longitude = ($weight1 * $location1->longitude + $weight2 * $location2->longitude) / $totalWeight;

        // Estimate accuracy (weighted harmonic mean)
        $estimatedAccuracy = $totalWeight > 0 ? 1 / ($weight1 / $accuracy1 + $weight2 / $accuracy2) : 0;

        // Create and return a new CellLocation object with the estimated position and accuracy
        return new CellLocation(
            latitude: $latitude,
            longitude: $longitude,
            accuracy: $estimatedAccuracy,
        );
    }

    /**
     * Get the triangulated location based on multiple CellData objects.
     *
     * This method retrieves the locations of multiple cell towers and performs
     * multilateration to estimate the device's position.
     *
     * @param CellData[] $cellDataArray Array of CellData objects.
     * @return CellLocation The estimated device location.
     * @throws RuntimeException If triangulation fails.
     */
    public function getTriangulatedLocation(array $cellDataArray): CellLocation
    {
        // Retrieve CellLocations for each CellData
        $cellLocations = [];
        foreach ($cellDataArray as $cellData) {
            $location = $this->getLocation($cellData);
            if ($location !== null) {
                $cellLocations[] = $location;
            }
        }

        // Check locations
        $numLocations = count($cellLocations);
        if ($numLocations === 0) {
            throw new RuntimeException('No locations found for triangulation.');
        } elseif ($numLocations === 1) {
            return $cellLocations[0];
        } elseif ($numLocations === 2) {
            return $this->calculateWeightedMidpoint($cellLocations[0], $cellLocations[1]);
        }

        // Use the first location as the reference point
        $referenceLatitude = $cellLocations[0]->latitude;
        $referenceLongitude = $cellLocations[0]->longitude;

        // Convert lat/lon to x/y coordinates relative to the reference point
        $points = [];
        foreach ($cellLocations as $location) {
            $coordinates = $this->latLonToXY($location->latitude, $location->longitude, $referenceLatitude, $referenceLongitude);
            $points[] = [
                'x' => $coordinates['x'],
                'y' => $coordinates['y'],
                'r' => $location->accuracy, // Accuracy as radius in meters
            ];
        }

        // Perform multilateration to estimate the device's position
        $estimatedXY = $this->multilaterate($points);

        // Convert estimated x/y back to latitude and longitude
        $estimatedLatLon = $this->xyToLatLon($estimatedXY['x'], $estimatedXY['y'], $referenceLatitude, $referenceLongitude);

        // Estimate accuracy (maximum residual)
        $maxResidual = 0;
        foreach ($points as $point) {
            $dx = $point['x'] - $estimatedXY['x'];
            $dy = $point['y'] - $estimatedXY['y'];
            $distance = sqrt($dx ** 2 + $dy ** 2);
            $residual = abs($distance - $point['r']);
            if ($residual > $maxResidual) {
                $maxResidual = $residual;
            }
        }

        $estimatedAccuracy = $maxResidual;

        // Return the estimated location
        return new CellLocation(
            // Round to 7 decimal places for consistency
            latitude: round($estimatedLatLon['lat'], $this::COORDINATE_PRECISION),
            longitude: round($estimatedLatLon['lon'], $this::COORDINATE_PRECISION),
            // Round to 2 decimal places for consistency
            accuracy: round($estimatedAccuracy, $this::ACCURACY_PRECISION),
        );
    }

    /**
     * Converts latitude and longitude to Cartesian x and y coordinates.
     *
     * This method calculates the distance and initial bearing from the reference point
     * to the given point and converts them to x and y coordinates.
     *
     * @param float $latitude Latitude of the point to convert.
     * @param float $longitude Longitude of the point to convert.
     * @param float $referenceLatitude Latitude of the reference point.
     * @param float $referenceLongitude Longitude of the reference point.
     * @return array Associative array with keys 'x' and 'y' representing Cartesian coordinates in meters.
     */
    private function latLonToXY(float $latitude, float $longitude, float $referenceLatitude, float $referenceLongitude): array
    {
        // Create Point objects
        $point = new Point($latitude, $longitude);
        $referencePoint = new Point($referenceLatitude, $referenceLongitude);

        // Calculate distance using the Haversine formula
        $route = new Route($referencePoint, $point, $this->calculator);
        $distance = $this->calculator->calculate($route);

        // Calculate initial bearing
        $bearing = $this->calculateInitialBearing($referenceLatitude, $referenceLongitude, $latitude, $longitude);

        // Convert bearing to radians
        $bearingRad = deg2rad($bearing);

        // Convert polar coordinates to Cartesian coordinates
        $x = $distance * sin($bearingRad);
        $y = $distance * cos($bearingRad);

        return ['x' => $x, 'y' => $y];
    }

    /**
     * Converts Cartesian x and y coordinates back to latitude and longitude.
     *
     * This method converts x and y coordinates to distance and bearing and computes
     * the destination point from the reference point.
     *
     * @param float $x X coordinate in meters.
     * @param float $y Y coordinate in meters.
     * @param float $referenceLatitude Latitude of the reference point.
     * @param float $referenceLongitude Longitude of the reference point.
     * @return array Associative array with keys 'lat' and 'lon' representing latitude and longitude.
     */
    private function xyToLatLon(float $x, float $y, float $referenceLatitude, float $referenceLongitude): array
    {
        // Calculate distance and bearing from x and y
        $distance = sqrt($x ** 2 + $y ** 2);
        $bearingRad = atan2($x, $y); // Note the order of x and y for atan2
        $bearing = rad2deg($bearingRad);

        // Calculate destination point
        $destination = $this->calculateDestinationPoint($referenceLatitude, $referenceLongitude, $distance, $bearing);

        return [
            'lat' => $destination['latitude'],
            'lon' => $destination['longitude'],
        ];
    }

    /**
     * Performs multilateration to estimate the position based on multiple known locations and distances.
     *
     * Multilateration determines the position of a point based on the distances from multiple known locations.
     * It uses the known positions of cell towers and the signal accuracy to estimate the position of the device.
     *
     * @param array $points Array of points, each with keys 'x', 'y', and 'r' (radius).
     * @return array Estimated x and y coordinates.
     * @throws RuntimeException If the matrix is singular or cannot proceed.
     */
    private function multilaterate(array $points): array
    {
        $numPoints = count($points);
        if ($numPoints < 3) {
            throw new RuntimeException('At least three points are required for multilateration.');
        }

        // Use the first point as a reference
        $x1 = $points[0]['x'];
        $y1 = $points[0]['y'];
        $r1 = $points[0]['r'];

        $A = []; // Coefficient matrix
        $b = []; // Constant terms vector

        // Construct the linear equations based on circle equations
        for ($i = 1; $i < $numPoints; $i++) {
            $xi = $points[$i]['x'];
            $yi = $points[$i]['y'];
            $ri = $points[$i]['r'];

            // Coefficients for x and y
            $A[] = [
                2 * ($xi - $x1),
                2 * ($yi - $y1),
            ];

            // Constants
            $b[] = ($r1 ** 2 - $ri ** 2) - ($x1 ** 2 - $xi ** 2) - ($y1 ** 2 - $yi ** 2);
        }

        // Transpose A
        $A_transposed = $this->transpose($A);

        // Compute A^T * A and A^T * b
        $AtA = $this->multiplyMatrices($A_transposed, $A); // 2x2 matrix
        $Atb = $this->multiplyMatrixVector($A_transposed, $b); // 2x1 vector

        // Regularization parameter (lambda) to handle singular matrices
        $lambda = 1e-5; // Small positive value

        // Add regularization term to the diagonal elements of AtA
        $AtA[0][0] += $lambda;
        $AtA[1][1] += $lambda;

        // Compute determinant of AtA
        $determinant = $AtA[0][0] * $AtA[1][1] - $AtA[0][1] * $AtA[1][0];

        if (abs($determinant) < 1e-10) {
            throw new RuntimeException('Matrix is singular; cannot proceed.');
        }

        // Compute inverse of AtA
        $inverseAtA = [
            [$AtA[1][1] / $determinant, -$AtA[0][1] / $determinant],
            [-$AtA[1][0] / $determinant, $AtA[0][0] / $determinant],
        ];

        // Calculate the estimated position [x; y]
        $estimatedPosition = [
            $inverseAtA[0][0] * $Atb[0] + $inverseAtA[0][1] * $Atb[1],
            $inverseAtA[1][0] * $Atb[0] + $inverseAtA[1][1] * $Atb[1],
        ];

        return ['x' => $estimatedPosition[0], 'y' => $estimatedPosition[1]];
    }

    /**
     * Transposes a matrix (rows become columns and vice versa).
     *
     * @param array $matrix The matrix to transpose.
     * @return array The transposed matrix.
     */
    private function transpose(array $matrix): array
    {
        $transposed = [];
        foreach ($matrix as $row) {
            foreach ($row as $key => $value) {
                $transposed[$key][] = $value;
            }
        }
        return $transposed;
    }

    /**
     * Multiplies two matrices.
     *
     * @param array $matrixA First matrix.
     * @param array $matrixB Second matrix.
     * @return array The resulting matrix after multiplication.
     */
    private function multiplyMatrices(array $matrixA, array $matrixB): array
    {
        $result = [];
        $rowsA = count($matrixA);
        $colsA = count($matrixA[0]);
        $colsB = count($matrixB[0]);

        for ($i = 0; $i < $rowsA; $i++) {
            for ($j = 0; $j < $colsB; $j++) {
                $sum = 0;
                for ($k = 0; $k < $colsA; $k++) {
                    $sum += $matrixA[$i][$k] * $matrixB[$k][$j];
                }
                $result[$i][$j] = $sum;
            }
        }

        return $result;
    }

    /**
     * Multiplies a matrix by a vector.
     *
     * @param array $matrix The matrix.
     * @param array $vector The vector.
     * @return array The resulting vector after multiplication.
     */
    private function multiplyMatrixVector(array $matrix, array $vector): array
    {
        $result = [];
        $rows = count($matrix);
        $cols = count($matrix[0]);

        for ($i = 0; $i < $rows; $i++) {
            $sum = 0;
            for ($j = 0; $j < $cols; $j++) {
                $sum += $matrix[$i][$j] * $vector[$j];
            }
            $result[] = $sum;
        }

        return $result;
    }

    /**
     * Calculates the initial bearing from one point to another.
     *
     * @param float $lat1 Latitude of the first point in degrees.
     * @param float $lon1 Longitude of the first point in degrees.
     * @param float $lat2 Latitude of the second point in degrees.
     * @param float $lon2 Longitude of the second point in degrees.
     * @return float Bearing in degrees from the first point to the second.
     */
    private function calculateInitialBearing(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        // Convert degrees to radians
        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLonRad = deg2rad($lon2 - $lon1);

        // Calculate initial bearing
        $y = sin($deltaLonRad) * cos($lat2Rad);
        $x = cos($lat1Rad) * sin($lat2Rad) - sin($lat1Rad) * cos($lat2Rad) * cos($deltaLonRad);
        $bearingRad = atan2($y, $x);

        // Convert radians to degrees and normalize to 0-360
        return fmod(rad2deg($bearingRad) + 360, 360);
    }

    /**
     * Calculates the destination point given a starting point, distance, and bearing.
     *
     * @param float $lat Latitude of the starting point in degrees.
     * @param float $lon Longitude of the starting point in degrees.
     * @param float $distance Distance to travel in meters.
     * @param float $bearing Bearing in degrees.
     * @return array Associative array with keys 'latitude' and 'longitude'.
     */
    private function calculateDestinationPoint(float $lat, float $lon, float $distance, float $bearing): array
    {
        $earthRadius = EarthRadius::MEAN; // Earth's radius in meters

        // Convert degrees to radians
        $latRad = deg2rad($lat);
        $lonRad = deg2rad($lon);
        $bearingRad = deg2rad($bearing);

        // Angular distance
        $angularDistance = $distance / $earthRadius;

        // Calculate destination point
        $lat2Rad = asin(
            sin($latRad) * cos($angularDistance) +
            cos($latRad) * sin($angularDistance) * cos($bearingRad)
        );

        $lon2Rad = $lonRad + atan2(
                sin($bearingRad) * sin($angularDistance) * cos($latRad),
                cos($angularDistance) - sin($latRad) * sin($lat2Rad)
            );

        // Convert radians back to degrees
        $latitude = rad2deg($lat2Rad);
        $longitude = rad2deg($lon2Rad);

        return [
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];
    }
}
