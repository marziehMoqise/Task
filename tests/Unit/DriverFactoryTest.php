<?php

namespace Tests\Unit;

use App\Factories\DriverFactory;
use App\Http\Driver\CitynetDriver;
use App\Http\Driver\MoghimDriver;
use App\Http\Driver\PartoDriver;
use PHPUnit\Framework\TestCase;

class DriverFactoryTest extends TestCase
{
    public function testCreateValidDriver()
    {
        $factory = new DriverFactory();

        $driver = $factory->create('citynet');
        $this->assertInstanceOf(CitynetDriver::class, $driver);

        $driver = $factory->create('moghim');
        $this->assertInstanceOf(MoghimDriver::class, $driver);

        $driver = $factory->create('parto');
        $this->assertInstanceOf(PartoDriver::class, $driver);
    }

    public function testCreateInvalidDriver()
    {
        $factory = new DriverFactory();

        $this->expectException(\InvalidArgumentException::class);
        $factory->create('invalidDriver');
    }
}
