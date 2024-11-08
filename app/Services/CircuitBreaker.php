<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CircuitBreaker
{
    protected string $serviceName;
    protected int $threshold;
    protected int $successThreshold;
    protected int $timeout;
    protected int $rateLimit;

    protected array $fallbackData = [];

    public function __construct(string $serviceName = 'default')
    {
        $this->serviceName = $serviceName;
        $this->threshold = config('circuitBreaker.error_threshold', 5);
        $this->successThreshold = config('circuitBreaker.success_threshold', 3);
        $this->timeout = config('circuitBreaker.timeout', 20);
        $this->rateLimit = config('circuitBreaker.rate_limit', 10);
    }

    public function isAvailable(): bool
    {
        $state = Cache::get("{$this->serviceName}_state", 'closed');
        $isDisabled = Cache::get("{$this->serviceName}_disabled", false);

        if ($isDisabled) {
            Log::info("{$this->serviceName} is disabled.");
            return false;
        }

        Log::info("Checking availability for {$this->serviceName}: Current state is {$state}");

        $requests = Cache::get("{$this->serviceName}_requests", 0);
        if ($requests >= $this->rateLimit && $state === 'half_open') {
            Log::warning("Rate limit exceeded for {$this->serviceName}. Requests: {$requests}");

            return false;
        }

        if ($state === 'open') {
            $lastAttempt = Cache::get("{$this->serviceName}_last_attempt");

            if (Carbon::now()->diffInMinutes($lastAttempt) >= $this->timeout) {
                $this->setCache("{$this->serviceName}_state", 'half_open');
                Log::info("{$this->serviceName} state changed to half_open after timeout");

                return true;
            }

            Log::info("{$this->serviceName} is in open state, waiting for timeout");

            return false;
        }

        return true;
    }

    public function reportFailure(): void
    {
        $this->incrementCache("{$this->serviceName}_failures");

        Log::error("Failure reported for {$this->serviceName}. Failures: " . Cache::get("{$this->serviceName}_failures"));

        if (Cache::get("{$this->serviceName}_failures") >= $this->threshold) {
            $this->setCache("{$this->serviceName}_state", 'open');
            $this->setCache("{$this->serviceName}_last_attempt", Carbon::now());
            Log::error("{$this->serviceName} state changed to open due to error threshold");
        }
    }

    public function reportSuccess(): void
    {
        $this->incrementCache("{$this->serviceName}_successes");

        Log::info("Success reported for {$this->serviceName}. Successes: " . Cache::get("{$this->serviceName}_successes"));

        if (Cache::get("{$this->serviceName}_successes") >= $this->successThreshold) {
            $this->setCache("{$this->serviceName}_state", 'closed');
            Cache::forget("{$this->serviceName}_failures");
            Cache::forget("{$this->serviceName}_successes");
            Cache::forget("{$this->serviceName}_requests");

            Log::info("{$this->serviceName} state changed to closed after success threshold");
        }
    }

    public function recordRequest(): void
    {
        $this->incrementCache("{$this->serviceName}_requests");

        Log::info("Request recorded for {$this->serviceName}. Total requests: " . Cache::get("{$this->serviceName}_requests"));
    }

    private function setCache(string $key, $value, $expiration = null): void
    {
        if ($expiration === null) {
            Cache::put($key, $value);
        } else {
            Cache::put($key, $value, now()->addMinutes($expiration));
        }
    }

    private function incrementCache(string $key): void
    {
        Cache::increment($key);
    }

    public function setFallbackData(array $data): void
    {
        $this->fallbackData = $data;
    }

    public function getFallbackData(): array
    {
        return $this->fallbackData;
    }
}
