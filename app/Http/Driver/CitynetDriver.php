<?php

namespace App\Http\Driver;

use Illuminate\Support\Facades\Http;
use Exception;

class CitynetDriver implements DriverInterface
{
    public function getApiUrl(): string
    {
        return 'https://newcash.me/api-beta/citynet';
    }

    public function search(): array
    {
        try {
            $response = Http::timeout(3)->get($this->getApiUrl());
            return $response->json();
        } catch (Exception $e) {
            return ['error' => 'Failed to fetch data', 'message' => $e->getMessage()];
        }
    }
}
