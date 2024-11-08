<?php

namespace App\Enums;

use App\Enums\Traits\Values;

/**
 * DriverName
 */
enum DriverName : string
{
    use Values;

    case Citynet = 'citynet';
    case Moghim = 'moghim';
    case Parto = 'parto';
}
