<?php 

declare(strict_types=1);

namespace Lounisbou\CellLocation;

class CellLocation {
    
    /**
     * CellLocation constructor.
     * 
     * @param float $latitude Latitude.
     * @param float $longitude Longitude.
     * @param float $accuracy Accuracy (in meters).
     * @param string|null $address Address (optional).
     */
    public function __construct(
        public float $latitude, 
        public float $longitude, 
        public float $accuracy,
        public ?string $address = null,
    )
    {
    }

    public function __toString() {
        return "Latitude: $this->latitude, Longitude: $this->longitude, Accuracy: $this->accuracy";
    }

    public function __serialize(): array {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'accuracy' => $this->accuracy,
            'address' => $this->address,
        ];
    }

    public function __unserialize(array $data): void {
        $this->latitude = $data['latitude'];
        $this->longitude = $data['longitude'];
        $this->accuracy = $data['accuracy'];
        $this->address = isset($data['address']) ? $data['address'] : null;
    }

}