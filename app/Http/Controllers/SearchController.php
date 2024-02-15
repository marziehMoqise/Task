<?php

namespace App\Http\Controllers;

use App\Http\Driver\CitynetDriver;
use App\Http\Driver\MoghimDriver;
use App\Http\Driver\PartoDriver;
use Illuminate\Support\Carbon;

class SearchController
{
    public function search()
    {
        $cityNetResults = (new CitynetDriver())->search();

        $partoResults = (new PartoDriver())->src();

        $moghimResults = (new MoghimDriver())->srch();

        $output = [];

        foreach ($cityNetResults as $cityNetResult) {
            $output[] = [
                'driver' => 'citynet',
                'number' => $cityNetResult['uuid'],
                'amount' => $cityNetResult['amount'],
                'time' => Carbon::createFromFormat('U', $cityNetResult['amount'])->format('Y-m-d h:i:s'),
                'from' => $cityNetResult['from'],
                'to' => $cityNetResult['to']
            ];
        }

        foreach ($partoResults as $partoResult) {
            $output[] = [
                'driver' => 'parto',
                'number' => $partoResult['fnum'],
                'amount' => $partoResult['amount'],
                'time' => $partoResult['time'],
                'from' => $partoResult['from'],
                'to' => $partoResult['to']
            ];
        }

        foreach ($moghimResults as $moghimResult) {
            $output[] = [
                'driver' => 'moghim',
                'number' => $moghimResult['flight_number'],
                'amount' => $moghimResult['amount'],
                'time' => $moghimResult['time'],
                'from' => $moghimResult['from'],
                'to' => $moghimResult['to']
            ];
        }

        return $output;
    }

}
