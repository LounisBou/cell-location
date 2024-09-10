<?php

declare(strict_types=1);

use Lounisbou\CellLocation\CellData;
use Lounisbou\CellLocation\CellLocator;
use Lounisbou\CellLocation\Enums\RadioType;
use Lounisbou\CellLocation\Services\OpenCellIDService;
use Lounisbou\CellLocation\Services\UnwiredLabsService;
use Lounisbou\CellLocation\Services\GoogleGeolocationService;

// Create cell data with known location data
$cellData = new CellData(
    mcc: 260,
    mnc: 2,
    lac: 10250,
    cellId: 26511,
    radioType: RadioType::GSM
);

test('findLocation returns error with invalid API key for OpenCellID service', function () use ($cellData) {
    // Create an instance of the OpenCellID service with an invalid API key
    $openCellIdService = new OpenCellIDService('invalid-api-key');

    // Create an instance of the CellLocator
    $cellLocator = new CellLocator($openCellIdService);

    // Expect getLocation to throw an exception
    $this->expectExceptionMessage('Geolocation service error: Code 2 - API Key not known: invalid-api-key');

    // Test with invalid API key
    $cellLocator->getLocation($cellData);
});

test('findLocation returns error with invalid API key for UnwiredLabs service', function () use ($cellData) {
    // Create an instance of the UnwiredLabs service with an invalid API key
    $unwiredLabsService = new UnwiredLabsService('invalid-api-key');

    // Create an instance of the CellLocator
    $cellLocator = new CellLocator($unwiredLabsService);

    // Expect getLocation to throw an exception
    $this->expectExceptionMessage('Geolocation service error: Invalid token - get a trial token by signing up for free here: http://my.unwiredlabs.com/trial');

    // Test with invalid API key
    $cellLocator->getLocation($cellData);
});

test('findLocation returns error with invalid API key for GoogleGeolocation service', function () use ($cellData) {
    // Create an instance of the GoogleGeolocationService with an invalid API key
    $googleGeolocationService = new GoogleGeolocationService('invalid-api-key');

    // Create an instance of the CellLocator
    $cellLocator = new CellLocator($googleGeolocationService);

    // Expect getLocation to throw an exception
    $this->expectExceptionMessage('Geolocation service error: Code 400 - API key not valid. Please pass a valid API key.');

    // Test with invalid API key
    $cellLocator->getLocation($cellData);
});
