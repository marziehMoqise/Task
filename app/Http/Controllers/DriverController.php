<?php

namespace App\Http\Controllers;

use App\Http\Requests\Driver\DriverStatusRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class DriverController extends Controller
{
    public function disableDriver(DriverStatusRequest $request): JsonResponse
    {
        $data = $request->validated();
        Cache::put("{$data['driverName']}_disabled", true);
        return response()->json(["message" => "{$data['driverName']} has been disabled."]);
    }

    public function enableDriver(DriverStatusRequest $request): JsonResponse
    {
        $data = $request->validated();
        Cache::put("{$data['driverName']}_disabled", false);
        return response()->json(["message" => "{$data['driverName']} has been enabled."]);
    }
}
