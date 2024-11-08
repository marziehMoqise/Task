<?php

namespace App\Enums\Traits;

/**
 * Api exception handler Trait
 *
 */
trait Values
{

    public static function values(): array
    {
        return array_column(static::cases(), 'value');
    }
}
