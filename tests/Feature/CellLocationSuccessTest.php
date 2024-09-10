<?php

use CellLocation\CellLocator;
use CellLocation\OpenCellIDService;
use CellLocation\UnwiredLabsService;
use CellLocation\GoogleGeolocationService;

test('findLocation works with OpenCellID service', function () {
    // Create an instance of the OpenCellID service
    $openCellIdService = new OpenCellIDService($_ENV['OPENCELLID_API_KEY']);

    // Create an instance of the CellLocator
    $cellLocator = new CellLocator($openCellIdService);

    // Expect the output to match the known latitude and longitude
    $this->expectOutputString('Latitude: 37.7749, Longitude: -122.4194' . PHP_EOL);
    
    // Test the findLocation method with known cell location data
    $cellLocator->findLocation(310, 410, 0, 0);
});

test('findLocation works with UnwiredLabs service', function () {
    // Create an instance of the UnwiredLabs service
    $unwiredLabsService = new UnwiredLabsService($_ENV['UNWIREDLABS_API_KEY']);

    // Create an instance of the CellLocator
    $cellLocator = new CellLocator($unwiredLabsService);

    // Expect the output to match the known latitude and longitude
    $this->expectOutputString('Latitude: 37.7749, Longitude: -122.4194' . PHP_EOL);
    
    // Test the findLocation method with known cell location data
    $cellLocator->findLocation(310, 410, 0, 0);
});

test('findLocation works with Google Geolocation service', function () {
    // Create an instance of the GoogleGeolocationService
    $googleMapsService = new GoogleGeolocationService($_ENV['GOOGLE_MAPS_API_KEY']);

    // Create an instance of the CellLocator
    $cellLocator = new CellLocator($googleMapsService);

    // Expect the output to match the known latitude and longitude
    $this->expectOutputString('Latitude: 37.7749, Longitude: -122.4194' . PHP_EOL);
    
    // Test the findLocation method with known cell location data
    $cellLocator->findLocation(310, 410, 0, 0);
});
