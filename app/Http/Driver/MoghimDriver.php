<?php

namespace App\Http\Driver;

use Illuminate\Support\Facades\Http;

class MoghimDriver
{

    public function srch()
    {
        $response = Http::get('https://newcash.me/api-beta/moghim');

        return json_decode($response, true);
    }

}
