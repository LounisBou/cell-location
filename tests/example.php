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
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . '/../.env');

// Import cell data
$cellDataArray = require_once __DIR__ . '/cells.php';

/* OpenCellIDService example */

// Create an instance of the service
$openCellIdService = new OpenCellIDService($_ENV['OPENCELLID_API_KEY']);
// Create an instance of the CellLocator
$cellLocator = new CellLocator($openCellIdService);
// Test the getLocation method with known cell location data
try {
    echo 'OpenCellIDService: ' . PHP_EOL;
    $cellLocation = $cellLocator->getLocation($cellDataArray[0]);
    // Check if the cell location is null
    if ($cellLocation === null) {
        echo "Cell location not found." . PHP_EOL;
        echo PHP_EOL;
    } else {
        // Print the cell location
        echo $cellLocation . PHP_EOL;
        echo PHP_EOL;
    }
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}

/* UnwiredLabsService example */

// Create an instance of the service
$openCellIdService = new UnwiredLabsService($_ENV['UNWIREDLABS_API_KEY']);
// Create an instance of the CellLocator
$cellLocator = new CellLocator($openCellIdService);
// Test the getLocation method with known cell location data
try {
    echo "UnwiredLabsService:" . PHP_EOL;
    $cellLocation = $cellLocator->getLocation($cellDataArray[0]);
    // Check if the cell location is null
    if ($cellLocation === null) {
        echo "Cell location not found." . PHP_EOL;
        echo PHP_EOL;
    } else {
        // Print the cell location
        echo $cellLocation . PHP_EOL;
        echo PHP_EOL;
    }
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}

/* GoogleGeolocationService example */

// Create an instance of the service
$openCellIdService = new GoogleGeolocationService($_ENV['GOOGLE_MAPS_API_KEY']);
// Create an instance of the CellLocator
$cellLocator = new CellLocator($openCellIdService);
// Test the getLocation method with known cell location data
try {
    echo "GoogleGeolocationService:" . PHP_EOL;
    $cellLocation = $cellLocator->getLocation($cellDataArray[0]);
    // Print the cell location
    echo $cellLocation . PHP_EOL;
    echo PHP_EOL;
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}

/* Triangulated location example */

// Create an instance of the CellLocator
$cellLocator = new CellLocator($openCellIdService);
// Test the getTriangulatedLocation method with the array of CellData objects
try {
    echo "Show all cell location using OpenCellID Service:" . PHP_EOL;
    foreach ($cellDataArray as $cellData) {
        // Get the cell location
        $cellLocation = $cellLocator->getLocation($cellData);
        // Check if the cell location is null
        if ($cellLocation === null) {
            echo "Cell location not found." . PHP_EOL;
            echo PHP_EOL;
        } else {
            // Print the cell location
            echo $cellLocation . PHP_EOL;
            echo PHP_EOL;
        }
    }
    echo "Triangulated location using OpenCellID Service :" . PHP_EOL;
    $cellLocation = $cellLocator->getTriangulatedLocation($cellDataArray);
    // Print the cell location
    echo $cellLocation . PHP_EOL;
    echo PHP_EOL;
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
}