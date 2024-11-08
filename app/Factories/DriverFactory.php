<?php

namespace App\Factories;

use App\Http\Driver\CitynetDriver;
use App\Http\Driver\PartoDriver;
use App\Http\Driver\MoghimDriver;
use App\Http\Driver\DriverInterface;
use App\Enums\DriverName;

class DriverFactory
{
    protected array $drivers = [];

    public function __construct()
    {
        $this->drivers = [
            DriverName::Citynet->value => new CitynetDriver(),
            DriverName::Moghim->value => new MoghimDriver(),
            DriverName::Parto->value => new PartoDriver(),
        ];
    }

    public function create(string $driverName): DriverInterface
    {
        if (!isset($this->drivers[$driverName])) {
            throw new \InvalidArgumentException("Unknown driver: {$driverName}");
        }

        return $this->drivers[$driverName];
    }

    public function getDrivers(): array
    {
        return $this->drivers;
    }
}
