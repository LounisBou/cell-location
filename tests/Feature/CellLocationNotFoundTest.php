<?php

use Lounisbou\CellLocation\CellLocator;
use Lounisbou\CellLocation\OpenCellIDService;
use Lounisbou\CellLocation\UnwiredLabsService;
use Lounisbou\CellLocation\GoogleGeolocationService;

test('findLocation returns not found with OpenCellID service', function () {
    // Create an instance of the OpenCellID service
    $openCellIdService = new OpenCellIDService($_ENV['OPENCELLID_API_KEY']);

    // Create an instance of the CellLocator
    $cellLocator = new CellLocator($openCellIdService);

    // Expect the output to be 'Location not found.'
    $this->expectOutputString('Location not found.');

    // Expect cell location to be null
    $this->assertNull($cellLocator->getLocation(0, 0, 0, 0));

});

test('findLocation returns not found with UnwiredLabs service', function () {
    // Create an instance of the UnwiredLabs service
    $unwiredLabsService = new UnwiredLabsService($_ENV['UNWIREDLABS_API_KEY']);

    // Create an instance of the CellLocator
    $cellLocator = new CellLocator($unwiredLabsService);

    // Expect cell location to be null
    $this->assertNull($cellLocator->getLocation(0, 0, 0, 0));
});

test('findLocation returns not found with GoogleGeolocation service', function () {
    // Create an instance of the GoogleGeolocationService
    $googleGeolocationService = new GoogleGeolocationService($_ENV['GOOGLE_API_KEY']);

    // Create an instance of the CellLocator
    $cellLocator = new CellLocator($googleGeolocationService);

    // Expect cell location to be null
    $this->assertNull($cellLocator->getLocation(0, 0, 0, 0));
});
