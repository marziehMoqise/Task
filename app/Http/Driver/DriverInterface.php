<?php

namespace App\Http\Driver;

interface DriverInterface
{
    public function search(): array;

    public function getApiUrl(): string;
}
