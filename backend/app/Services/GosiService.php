<?php

namespace App\Services;

use App\Classes\GosiEmployee;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
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
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \JsonException
     */
    public static function getEmployeeData(GosiEmployee $gosiEmployee): array
    {
        $service = new GosiService();

        if (auth()->user()->email != 'sara@ptc-ksa.net') {
            return false;
        }

        try {
            $response = $service->client->get(config('services.masdr.endpoint').'/mofeed/employment/v1/employee/employment-status/'.$gosiEmployee->getNinOrIqama(), [
                'cert' => storage_path('masdrcertificate/certificate.crt'),
                'ssl_key' => storage_path('masdrcertificate/certificate.key'),
            ]);
            // return json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
            $data= json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
            dd($data);
        } catch (RequestException $e) {
            if ($e->hasResponse() && $e->getResponse()->getStatusCode() == '400') {
                // return json_decode($e->getResponse()->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
                $data= json_decode($e->getResponse()->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
                dd($data);
            }
        }
    }
}
