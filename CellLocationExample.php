<?php

/**
 * Example script to demonstrate the CellLocation usage.
 * 
 * @author LounisBou
 * @version 1.0
 * @license MIT
 */

declare(strict_types=1);

use Lounisbou\CellLocation\CellLocator;
use Lounisbou\CellLocation\CellData;
use Lounisbou\CellLocation\Enums\RadioType;
use Lounisbou\CellLocation\Services\UnwiredLabsService;
use Lounisbou\CellLocation\Services\OpenCellIDService;
use Lounisbou\CellLocation\Services\GoogleGeolocationService;
use Symfony\Component\Dotenv\Dotenv;

// Load the Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables
$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . '/.env');

// CellData
$cellData = new CellData(
    mcc: 260,
    mnc: 2,
    lac: 10250,
    cellId: 2651,
    radioType: RadioType::GSM
);

/* OpenCellIDService example */

// Create an instance of the service
$openCellIdService = new OpenCellIDService($_ENV['OPENCELLID_API_KEY']);
// Create an instance of the CellLocator
$cellLocator = new CellLocator($openCellIdService);
// Test the findLocation method with known cell location data
try {
    echo 'OpenCellIDService: ' . PHP_EOL;
    $cellLocation = $cellLocator->getLocation($cellData);
    // Print the cell location
    echo $cellLocation . PHP_EOL;
    echo PHP_EOL;
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}

/* UnwiredLabsService example */

// Create an instance of the service
$openCellIdService = new UnwiredLabsService($_ENV['UNWIREDLABS_API_KEY']);
// Create an instance of the CellLocator
$cellLocator = new CellLocator($openCellIdService);
// Test the findLocation method with known cell location data
try {
    echo "UnwiredLabsService:" . PHP_EOL;
    $cellLocation = $cellLocator->getLocation($cellData);
    // Print the cell location
    echo $cellLocation . PHP_EOL;
    echo PHP_EOL;
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}

/* GoogleGeolocationService example */

// Create an instance of the service
$openCellIdService = new GoogleGeolocationService($_ENV['GOOGLE_MAPS_API_KEY']);
// Create an instance of the CellLocator
$cellLocator = new CellLocator($openCellIdService);
// Test the findLocation method with known cell location data
try {
    echo "GoogleGeolocationService:" . PHP_EOL;
    $cellLocation = $cellLocator->getLocation($cellData);
    // Print the cell location
    echo $cellLocation . ", Accuracy: " . $cellLocation->accuracy . PHP_EOL;
    echo PHP_EOL;
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}