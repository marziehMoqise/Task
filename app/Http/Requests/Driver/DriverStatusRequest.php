<?php

namespace App\Http\Requests\Driver;

use App\Enums\DriverName;
use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rule;

class DriverStatusRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'driverName' => ['required', 'string', Rule::in(DriverName::values())],
        ];
    }
}
