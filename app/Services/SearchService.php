<?php

namespace App\Services;

use App\Factories\DriverFactory;
use App\Http\Driver\DriverInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Utils;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SearchService
{
    protected array $drivers;
    protected CircuitBreaker $circuitBreaker;
    protected Client $client;

    public function __construct(DriverFactory $driverFactory, CircuitBreaker $circuitBreaker)
    {
        $this->drivers = $driverFactory->getDrivers();
        $this->circuitBreaker = $circuitBreaker;
        $this->client = new Client(['timeout' => 2, 'http_errors' => false]);
    }

    public function performSearch(): array
    {
        $output = [];
        $promises = [];

        foreach ($this->drivers as $name => $driver) {
            if ($this->isDriverDisabled($name)) {
                Log::warning("Service {$name} is disabled. Returning fallback data.");
                $output[] = $this->generateErrorResponse(
                    $name,
                    'Service disabled',
                    $this->circuitBreaker->getFallbackData()
                );
                continue;
            }

            $this->circuitBreaker->recordRequest();

            if (!$this->circuitBreaker->isAvailable($name)) {
                Log::warning("Service {$name} is not available. Returning fallback data.");
                $output[] = $this->generateErrorResponse(
                    $name,
                    'Service not available',
                    $this->circuitBreaker->getFallbackData()
                );
                continue;
            }

            $cacheKey = "{$name}_data";
            $cachedData = Cache::get($cacheKey);

            if ($cachedData) {
                $output[] = $this->generateSuccessResponse($name, 'Using cached data', $cachedData);
                continue;
            }

            $promises[$name] = $this->fetchData($driver, $name);
        }

        $results = Utils::settle($promises)->wait();
        foreach ($results as $name => $result) {
            $output[] = $this->processResult($result, $name);
        }

        return $output;
    }


    private function fetchData(DriverInterface $driver, string $name): PromiseInterface
    {
        return $this->client->getAsync($driver->getApiUrl())->then(
            function ($response) use ($name) {
                $data = json_decode($response->getBody(), true);
                $this->circuitBreaker->reportSuccess();
                Cache::put("{$name}_data", $data, now()->addMinutes(10));
                $this->circuitBreaker->setFallbackData($data);
                return $this->generateSuccessResponse($name, 'Request successful', $data);
            },
            function (\Exception $e) use ($name) {
                $this->circuitBreaker->reportFailure(); // گزارش شکست درخواست
                return $this->generateErrorResponse($name, 'Request failed: ' . $e->getMessage());
            }
        );
    }

    private function processResult($result, string $name): array
    {
        if ($result['state'] === 'fulfilled') {
            return $result['value'];
        } else {
            return [
                'driver' => $name,
                'status' => 'error',
                'message' => $result['reason']->getMessage(),
                'data' => [],
            ];
        }
    }

    private function generateSuccessResponse(string $driver, string $message, array $data): array
    {
        return [
            'driver' => $driver,
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ];
    }

    private function generateErrorResponse(string $driver, string $message, array $data = []): array
    {
        return [
            'driver' => $driver,
            'status' => 'error',
            'message' => $message,
            'data' => $data,
        ];
    }

    private function isDriverDisabled(string $driverName): bool
    {
        return Cache::get("{$driverName}_disabled", false);
    }
}


