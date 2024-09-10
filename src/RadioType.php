<?php

declare(strict_types=1);

namespace Lounisbou\CellLocation;

enum RadioType: string
{
    case GSM = 'gsm';
    case CDMA = 'cdma';
    case WCDMA = 'wcdma';
    case LTE = 'lte';
    case NR = 'nr'; // 5G New Radio

    /**
     * Check if the radio type is valid for Google and Unwired Labs APIs.
     *
     * @param string $radioType
     * @return bool
     */
    public static function isValidForGoogleAndUnwiredLabs(string $radioType): bool
    {
        return in_array($radioType, self::cases(), true);
    }
}
