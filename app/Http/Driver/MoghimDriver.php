<?php

namespace App\Http\Driver;

use Illuminate\Support\Facades\Http;
use Exception;

class MoghimDriver implements DriverInterface
{
    public function getApiUrl(): string
    {
        return 'https://newcash.me/api-beta/moghim';
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
