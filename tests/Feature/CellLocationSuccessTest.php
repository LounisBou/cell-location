<?php

declare(strict_types=1);

use Lounisbou\CellLocation\CellData;
use Lounisbou\CellLocation\CellLocation;
use Lounisbou\CellLocation\CellLocator;
use Lounisbou\CellLocation\Enums\RadioType;
use Lounisbou\CellLocation\Services\CellLocationServiceInterface;
use Lounisbou\CellLocation\Services\UnwiredLabsService;
use Lounisbou\CellLocation\Services\OpenCellIDService;
use Lounisbou\CellLocation\Services\GoogleGeolocationService;

// Import cell data
$cellDataArray = require_once __DIR__ . '/../cells.php';

test('getLocation works with OpenCellID service', function () use ($cellDataArray) {
    // Create an instance of the OpenCellID service
    $openCellIdService = new OpenCellIDService($_ENV['OPENCELLID_API_KEY']);

    // Create an instance of the CellLocator
    $cellLocator = new CellLocator($openCellIdService);
    
    // Test the findLocation method with known cell location data
    $cellLocation = $cellLocator->getLocation($cellDataArray[0]);
    
    // Expect the output to match the known latitude and longitude
    $this->assertEquals(52.231644, $cellLocation->latitude);
    $this->assertEquals(21.011933, $cellLocation->longitude);
    $this->assertEquals(900, $cellLocation->accuracy);
});

test('getLocation works with UnwiredLabs service', function () use ($cellDataArray) {
    // Create an instance of the UnwiredLabs service
    $unwiredLabsService = new UnwiredLabsService($_ENV['UNWIREDLABS_API_KEY']);

    // Create an instance of the CellLocator
    $cellLocator = new CellLocator($unwiredLabsService);
    
    // Test the findLocation method with known cell location data
    $cellLocation = $cellLocator->getLocation($cellDataArray[0]);

    // Expect the output to match the known latitude and longitude
    $this->assertEquals(52.230743, $cellLocation->latitude);
    $this->assertEquals(21.009712, $cellLocation->longitude);
    $this->assertEquals(900, $cellLocation->accuracy);
});

test('getLocation works with Google Geolocation service', function () use ($cellDataArray) {
    // Create an instance of the GoogleGeolocationService
    $googleMapsService = new GoogleGeolocationService($_ENV['GOOGLE_MAPS_API_KEY']);

    // Create an instance of the CellLocator
    $cellLocator = new CellLocator($googleMapsService);
    
    // Test the findLocation method with known cell location data
    $cellLocation = $cellLocator->getLocation($cellDataArray[0]);

    // Expect the output to match the known latitude and longitude
    $this->assertEquals(52.2314248, $cellLocation->latitude);
    $this->assertEquals(21.0105121, $cellLocation->longitude);
    $this->assertEquals(803, $cellLocation->accuracy);

});

test('getTriangulatedLocation works with OpenCellID service', function (
    CellLocationServiceInterface $cellIdService,
    CellLocation $cellLocation
) use ($cellDataArray) {
    // Create an instance of the CellLocator
    $cellLocator = new CellLocator($cellIdService);

    // Test the findLocation method with known cell location data
    $triangulatedCellLocation = $cellLocator->getTriangulatedLocation($cellDataArray);

    // Expect the output to match the known latitude and longitude
    $this->assertEquals($cellLocation->latitude, $triangulatedCellLocation->latitude);
    $this->assertEquals($cellLocation->longitude, $triangulatedCellLocation->longitude);
    $this->assertEquals($cellLocation->accuracy, $triangulatedCellLocation->accuracy);
})->with([
    'OpenCellID service' => [
        new OpenCellIDService($_ENV['OPENCELLID_API_KEY']),
        new CellLocation(latitude: 52.2343054, longitude: 21.0179447, accuracy: 394.84),
    ],
    'UnwiredLabs service' => [
        new UnwiredLabsService($_ENV['UNWIREDLABS_API_KEY']),
        new CellLocation(latitude: 52.2323488, longitude: 21.0174101, accuracy: 346.16),
    ],
    'Google Geolocation service' => [
        new GoogleGeolocationService($_ENV['GOOGLE_MAPS_API_KEY']),
        new CellLocation(latitude: 52.2330439, longitude: 21.0177922,  accuracy: 296.78),
    ],
]);
