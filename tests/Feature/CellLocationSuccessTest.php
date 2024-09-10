<?php

use Lounisbou\CellLocation\CellLocator;
use Lounisbou\CellLocation\RadioType;
use Lounisbou\CellLocation\Services\UnwiredLabsService;
use Lounisbou\CellLocation\Services\OpenCellIDService;
use Lounisbou\CellLocation\Services\GoogleGeolocationService;

test('findLocation works with OpenCellID service', function () {
    // Create an instance of the OpenCellID service
    $openCellIdService = new OpenCellIDService($_ENV['OPENCELLID_API_KEY']);

    // Create an instance of the CellLocator
    $cellLocator = new CellLocator($openCellIdService);

    // Expect the output to match the known latitude and longitude
    $this->expectOutputString('Latitude: 52.231644, Longitude: 21.011933');
    
    // Test the findLocation method with known cell location data
    $cellLocation = $cellLocator->getLocation(
        mcc:260, 
        mnc:2,
        lac:10250,
        cellId:26511,
        radioType:RadioType::GSM
    );
    
    // Print the cell location
    echo $cellLocation;
});

test('findLocation works with UnwiredLabs service', function () {
    // Create an instance of the UnwiredLabs service
    $unwiredLabsService = new UnwiredLabsService($_ENV['UNWIREDLABS_API_KEY']);

    // Create an instance of the CellLocator
    $cellLocator = new CellLocator($unwiredLabsService);

    // Expect the output to match the known latitude and longitude
    $this->expectOutputString('Latitude: 52.230743, Longitude: 21.009712');
    
    // Test the findLocation method with known cell location data
    $cellLocation = $cellLocator->getLocation(
        mcc:260, 
        mnc:2,
        lac:10250,
        cellId:26511,
        radioType:RadioType::GSM
    );

    // Print the cell location
    echo $cellLocation;
});

test('findLocation works with Google Geolocation service', function () {
    // Create an instance of the GoogleGeolocationService
    $googleMapsService = new GoogleGeolocationService($_ENV['GOOGLE_MAPS_API_KEY']);

    // Create an instance of the CellLocator
    $cellLocator = new CellLocator($googleMapsService);

    // Expect the output to match the known latitude and longitude
    $this->expectOutputString('Latitude: 52.231644, Longitude: 21.011933');
    
    // Test the findLocation method with known cell location data
    $cellLocation = $cellLocator->getLocation(
        mcc:260, 
        mnc:2,
        lac:10250,
        cellId:26511,
        radioType:RadioType::GSM
    );

    // Print the cell location
    echo $cellLocation;
});
