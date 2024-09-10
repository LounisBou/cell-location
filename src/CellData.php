<?php

declare(strict_types=1);

namespace Lounisbou\CellLocation;

class CellData{

    /**
     * Mobile Country Code
     * 
     * @var int
     */
    public int $mcc;

    /**
     * Mobile Network Code
     * 
     * @var int
     */
    public int $mnc;

    /**
     * Location Area Code
     * 
     * @var int
     */
    public int $lac;

    /**
     * Cell ID
     * 
     * @var int
     */
    public int $cellId;

    /**
     * Radio type (GSM, CDMA, WCDMA, LTE)
     * 
     * @var RadioType
     */
    public RadioType $radioType;

    /**
     * Constructor to initialize the cell data.
     * 
     * @param int $mcc Mobile Country Code
     * @param int $mnc Mobile Network Code
     * @param int|string $lac Location Area Code
     * @param int|string $cellId Cell ID
     * @param RadioType $radioType Radio type (GSM, CDMA, WCDMA, LTE) [default: GSM]
     */
    public function __construct(int $mcc, int $mnc, int|string $lac, int|string $cellId, RadioType $radioType = RadioType::GSM)
    {
        $this->mcc = $mcc;
        $this->mnc = $mnc;
        $this->lac = self::convertLacToInt($lac);
        $this->cellId = self::convertCellIdToInt($cellId);
        $this->radioType = $radioType;
    }
    
    /**
     * Get the cell data as an array.
     * 
     * @return array Cell data
     */
    public function toArray(): array
    {
        return [
            'mcc' => $this->mcc,
            'mnc' => $this->mnc,
            'lac' => $this->lac,
            'cellId' => $this->cellId,
            'radioType' => $this->radioType->value,
        ];
    }

    /**
     * Get the cell data as a string.
     * 
     * @return string Cell data
     */
    public function __toString(): string
    {
        return "MCC: $this->mcc, MNC: $this->mnc, LAC: $this->lac, Cell ID: $this->cellId, Radio Type: $this->radioType";
    }

    /**
     * Serialize the cell data to an array.
     * 
     * @return array Serialized cell data
     */
    public function __serialize(): array
    {
        return $this->toArray();
    }

    /**
     * Unserialize the cell data from an array.
     * 
     * @param array $data Serialized cell data
     */
    public function __unserialize(array $data): void
    {
        $this->mcc = $data['mcc'];
        $this->mnc = $data['mnc'];
        $this->lac = $data['lac'];
        $this->cellId = $data['cellId'];
        $this->radioType = new RadioType($data['radioType']);
    }

    /**
     * Convert LAC to int if it is a hex string.
     * 
     * @param string|int $lac Location Area Code
     * @return int Location Area Code as an integer
     */
    public static function convertLacToInt(string|int $lac): int
    {
        return is_string($lac) && preg_match('/^[0-9A-Fa-f]+$/', $lac) ? hexdec($lac) : (int) $lac;
    }

    /**
     * Convert Cell ID to int if it is a hex string.
     * 
     * @param string|int $cellId Cell ID
     * @return int Cell ID as an integer
     */
    public static function convertCellIdToInt(string|int $cellId): int
    {
        return is_string($cellId) && preg_match('/^[0-9A-Fa-f]+$/', $cellId) ? hexdec($cellId) : (int) $cellId;
    }
    
}