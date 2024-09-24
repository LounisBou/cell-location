<?php

declare(strict_types=1);

use Lounisbou\CellLocation\CellData;
use Lounisbou\CellLocation\Enums\RadioType;

// Create cell data with known location data
$cellData = new CellData(
    mcc: 260,
    mnc: 2,
    lac: 10250,
    cellId: 26511,
    radioType: RadioType::GSM
);

$cellData2 = new CellData(
    mcc: 260,
    mnc: 2,
    lac: 10250,
    cellId: 21771,
    radioType: RadioType::GSM
);

$cellData3 = new CellData(
    mcc: 260,
    mnc: 2,
    lac: 58120,
    cellId: 41964,
    radioType: RadioType::GSM
);

// Export cell data
return [
    $cellData,
    $cellData2,
    $cellData3
];