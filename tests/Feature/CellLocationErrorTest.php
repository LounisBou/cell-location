<?php

use Lounisbou\CellLocation\CellLocator;
use Lounisbou\CellLocation\OpenCellIDService;
use Lounisbou\CellLocation\UnwiredLabsService;
use Lounisbou\CellLocation\GoogleGeolocationService;

test('findLocation returns error with invalid API key for OpenCellID service', function () {
    // Create an instance of the OpenCellID service with an invalid API key
    $openCellIdService = new OpenCellIDService('invalid-api-key');

    // Create an instance of the CellLocator
    $cellLocator = new CellLocator($openCellIdService);

    // Expect the output to be an error message
    $this->expectOutputString('Error: Failed to connect to the OpenCellID API.' . PHP_EOL);

    // Test with invalid API key
    $cellLocator->findLocation(310, 410, 0, 0);
});

test('findLocation returns error with invalid API key for UnwiredLabs service', function () {
    // Create an instance of the UnwiredLabs service with an invalid API key
    $unwiredLabsService = new UnwiredLabsService('invalid-api-key');

    // Create an instance of the CellLocator
    $cellLocator = new CellLocator($unwiredLabsService);

    // Expect the output to be an error message
    $this->expectOutputString('Error: Failed to connect to the UnwiredLabs API.' . PHP_EOL);

    // Test with invalid API key
    $cellLocator->findLocation(310, 410, 0, 0);
});

test('findLocation returns error with invalid API key for GoogleGeolocation service', function () {
    // Create an instance of the GoogleGeolocationService with an invalid API key
    $googleGeolocationService = new GoogleGeolocationService('invalid-api-key');

    // Create an instance of the CellLocator
    $cellLocator = new CellLocator($googleGeolocationService);

    // Expect the output to be an error message
    $this->expectOutputString('Error: Failed to connect to the Google Geolocation API.' . PHP_EOL);

    // Test with invalid API key
    $cellLocator->findLocation(310, 410, 0, 0);
});
