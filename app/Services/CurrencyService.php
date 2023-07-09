<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Kreait\Firebase\Exception\ApiException;

class CurrencyService
{
    /**
     * Client
     */
    protected Client $client;

    /**
     * Create a new instance.
     */
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => config('api-url.fixer')
        ]);
    }

    /**
     * Get latest rates.
     */
    public function getRates(string $baseCurrency): JsonResponse
    {
        $response = null;

        try {
            $response = $this->client->request('GET', '/fixer/latest', [
                'headers' => [
                    'apikey' => config('secrets.api-layer')
                ],
                'query' => [
                    'base' => $baseCurrency
                ]
            ]);
        } catch (RequestException $ex) {
            logger()->error($ex->getMessage());

            return response()->json([
                'message' => 'Something went wrong.',
                'exception' => $ex->getMessage()
            ], 500);
        }

        return response()->json(
            json_decode($response->getBody()->getContents(), true),
            $response->getStatusCode()
        );
    }

    /**
     * Get Cached Rates.
     */
    public function getCachedRates(string $baseCurrency): array
    {
        $cachePrefix = 'RATES';
        $key = "{$cachePrefix}_{$baseCurrency}";

        return Cache::remember($key, now()->endOfDay(), function () use ($baseCurrency) {
            $response = $this->getRates($baseCurrency);

            if (! $response->isSuccessful()) {
                throw new ApiException(data_get($response->getOriginalContent(), 'message'), 429);
            }

            $rates = data_get($response->getOriginalContent(), 'rates', []);

            return $rates;
        });
    }

    /**
     * Convert Rates
     */
    public function convertRates(string $baseCurrency, string $toCurrency)
    {
        return data_get($this->getCachedRates($baseCurrency), $toCurrency, 0);
    }
}
