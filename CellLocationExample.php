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
use Lounisbou\CellLocation\RadioType;
use Lounisbou\CellLocation\Services\UnwiredLabsService;
use Symfony\Component\Dotenv\Dotenv;

// Load the Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables
$dotenv = new Dotenv();
$dotenv->loadEnv(__DIR__ . '/.env');

// Create an instance of the service
$openCellIdService = new UnwiredLabsService($_ENV['UNWIREDLABS_API_KEY']);

// Create an instance of the CellLocator
$cellLocator = new CellLocator($openCellIdService);

// Test the findLocation method with known cell location data
try {
    $cellLocation = $cellLocator->getLocation(
        mcc:260, 
        mnc:2,
        lac:10250,
        cellId:0,
        radioType:RadioType::GSM
    );
    // Print the cell location
    echo $cellLocation . PHP_EOL;
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
