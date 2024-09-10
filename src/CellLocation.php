<?php 

declare(strict_types=1);

namespace Lounisbou\CellLocation;

class CellLocation {



    public function __construct(private float $latitude, private float $longitude)
    {
    }

    public function __toString() {
        return "Latitude: $this->latitude, Longitude: $this->longitude";
    }

    public function __serialize(): array {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude
        ];
    }

    public function __unserialize(array $data): void {
        $this->latitude = $data['latitude'];
        $this->longitude = $data['longitude'];
    }

}