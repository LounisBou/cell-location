<?php 

declare(strict_types=1);

namespace Lounisbou\CellLocation;

class CellLocation {

    public int $latitude;
    public int $longitude;

    public function __construct(int $latitude, int $longitude) {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
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