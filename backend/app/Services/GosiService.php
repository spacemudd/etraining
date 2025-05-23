<?php

namespace App\Services;

use App\Classes\GosiEmployee;
use App\Models\GosiEmployeeData;
use App\Models\AppSetting;
use App\Models\RequestCounter;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use Illuminate\Support\Facades\DB;
use kamermans\OAuth2\GrantType\ClientCredentials;
use kamermans\OAuth2\OAuth2Middleware;

class GosiService
{
    private $client;

    public function __construct()
    {
        $reauth_client = new Client([
            'base_uri' => config('services.masdr.endpoint') .'/token/v1/accesstoken?grant_type=client_credentials',
            'timeout' => 5,
            'connect_timeout' => 5,
            'defaults' => [
                'config' => [
                    'curl' => [
                        CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2
                    ]
                ]
            ]
        ]);

        $reauth_config = [
            "client_id" => config('services.masdr.client_id'),
            "client_secret" => config('services.masdr.client_secret'),
            "scope" => "read_write",
            "state" => time(),
        ];

        $grant_type = new ClientCredentials($reauth_client, $reauth_config);
        $oauth = new OAuth2Middleware($grant_type);
        $stack = HandlerStack::create();
        $stack->push($oauth);

        $this->client = new Client([
            'handler' => $stack,
            'auth'    => 'oauth',
        ]);
    }

    /**
     *
     * @param \App\Classes\GosiEmployee $gosiEmployee
     * @param bool $forceFresh
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public static function getEmployeeData(GosiEmployee $gosiEmployee, bool $forceFresh = false): array
    {
        $ninOrIqama = $gosiEmployee->getNinOrIqama();

        $existingData = GosiEmployeeData::where('nin_or_iqama', $ninOrIqama)->first();

        if (!$forceFresh && $existingData) {
            return array_merge(
                json_decode($existingData->data, true),
                ['updated_at' => $existingData->updated_at->toIso8601String()]
            );
        }

        $currentMonth = now()->format('Y-m');
        $requestCount = RequestCounter::firstOrCreate(
            ['month' => $currentMonth],
            ['count' => 0]
        );

        $maxMonthlyLimit = cache()->remember('gosi_monthly_requests_limit', 600, function () {
            return (int) (AppSetting::where('name', 'gosi_monthly_requests')->value('value') ?? 600);
        });
        if ($requestCount->count >= $maxMonthlyLimit) {
            throw new \Exception('Monthly request limit reached.');
        }

        $service = new GosiService();

        try {
            $response = $service->client->get(config('services.masdr.endpoint').'/mofeed/employment/v1/employee/employment-status/'.$ninOrIqama, [
                'cert' => storage_path('masdrcertificate/certificate.crt'),
                'ssl_key' => storage_path('masdrcertificate/certificate.key'),
            ]);

            $data = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

            GosiEmployeeData::updateOrCreate(
                ['nin_or_iqama' => $ninOrIqama],
                array_merge([
                    'data' => json_encode($data, JSON_THROW_ON_ERROR)
                ], $gosiEmployee->getReasons())
            );

            $requestCount->increment('count');

            return $data;
        } catch (RequestException $e) {
            if ($e->hasResponse() && $e->getResponse()->getStatusCode() == '400') {
                return json_decode($e->getResponse()->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
            }
            throw $e;
        }
    }
}
