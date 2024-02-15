<?php

namespace App\Http\Driver;

use Illuminate\Support\Facades\Http;

class PartoDriver
{

    public function src(){
        $response = Http::get('https://newcash.me/api-beta/parto');

        return json_decode($response, true);
    }

}
