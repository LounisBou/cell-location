<?php

declare(strict_types=1);

use Lounisbou\CellLocation\CellData;
use Lounisbou\CellLocation\CellLocator;
use Lounisbou\CellLocation\Enums\RadioType;
use Lounisbou\CellLocation\Services\UnwiredLabsService;
use Lounisbou\CellLocation\Services\OpenCellIDService;
use Lounisbou\CellLocation\Services\GoogleGeolocationService;

// Create cell data with known location data
$cellData = new CellData(
    mcc: 260,
    mnc: 2,
    lac: 10250,
    cellId: 26511,
    radioType: RadioType::GSM
);

test('findLocation works with OpenCellID service', function () use ($cellData) {
    // Create an instance of the OpenCellID service
    $openCellIdService = new OpenCellIDService($_ENV['OPENCELLID_API_KEY']);

    // Create an instance of the CellLocator
    $cellLocator = new CellLocator($openCellIdService);

    // Expect the output to match the known latitude and longitude
    $this->expectOutputString('Latitude: 52.231644, Longitude: 21.011933, Accuracy: 900');
    
    // Test the findLocation method with known cell location data
    $cellLocation = $cellLocator->getLocation($cellData);
    
    // Print the cell location
    echo $cellLocation;
});

test('findLocation works with UnwiredLabs service', function () use ($cellData) {
    // Create an instance of the UnwiredLabs service
    $unwiredLabsService = new UnwiredLabsService($_ENV['UNWIREDLABS_API_KEY']);

    // Create an instance of the CellLocator
    $cellLocator = new CellLocator($unwiredLabsService);

    // Expect the output to match the known latitude and longitude
    $this->expectOutputString('Latitude: 52.230743, Longitude: 21.009712, Accuracy: 900');
    
    // Test the findLocation method with known cell location data
    $cellLocation = $cellLocator->getLocation($cellData);

    // Print the cell location
    echo $cellLocation;
});

test('findLocation works with Google Geolocation service', function () use ($cellData) {
    // Create an instance of the GoogleGeolocationService
    $googleMapsService = new GoogleGeolocationService($_ENV['GOOGLE_MAPS_API_KEY']);

    // Create an instance of the CellLocator
    $cellLocator = new CellLocator($googleMapsService);

    // Expect the output to match the known latitude and longitude
    $this->expectOutputString('Latitude: 52.2314248, Longitude: 21.0105121, Accuracy: 803');
    
    // Test the findLocation method with known cell location data
    $cellLocation = $cellLocator->getLocation($cellData);

    // Print the cell location
    echo $cellLocation;
});
