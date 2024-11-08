<?php

namespace Tests\Feature;

use Tests\TestCase;

class SearchControllerTest extends TestCase
{
    public function testSearch()
    {
        $response = $this->getJson('/api/search');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'driver',
                    'status',
                    'message',
                    'data',
                ]
            ]);
    }
}
