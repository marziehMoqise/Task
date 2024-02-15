<?php

namespace App\Http\Driver;

use Illuminate\Support\Facades\Http;

class CitynetDriver
{
    public function search()
    {
        $response = Http::get('https://newcash.me/api-beta/citynet');

        return json_decode($response, true);
    }

}
