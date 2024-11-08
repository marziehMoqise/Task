<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class DriverControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testDisableDriver()
    {
        $response = $this->postJson('/api/driver/disable', ['driverName' => 'citynet']);

        $response->assertStatus(200)
            ->assertJson(['message' => 'citynet has been disabled.']);

        $this->assertTrue(Cache::get('citynet_disabled'));
    }

    public function testEnableDriver()
    {
        Cache::put('citynet_disabled', true);

        $response = $this->postJson('/api/driver/enable', ['driverName' => 'citynet']);

        $response->assertStatus(200)
            ->assertJson(['message' => 'citynet has been enabled.']);

        $this->assertFalse(Cache::get('citynet_disabled'));
    }
}
