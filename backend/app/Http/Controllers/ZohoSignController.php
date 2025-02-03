<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ZohoSignController extends Controller
{
    private $client_id = "1000.4BBV31SXRHK4VXDJS6TV985E2LWLWQ";
    private $client_secret = "974da681c0cb571ff47acea59bbb6f8de2fa9b0b2c";
    private $refresh_token = "1000.67edc994ecafad1b194805703a03e272.303c8c8122c654709f7e0e837a4180fd";
    private $redirect_uri = "https%3A%2F%2Fsign.zoho.com";

    private function getAccessToken()
    {
        $response = Http::asForm()->post("https://accounts.zoho.sa/oauth/v2/token", [
            "refresh_token" => $this->refresh_token,
            "client_id" => $this->client_id,
            "client_secret" => $this->client_secret,
            "redirect_uri" => $this->redirect_uri,
            "grant_type" => "refresh_token",
        ]);
        
        //  dd($response->json()['access_token'] ?? null) ; 

        return $response->json()['access_token'] ?? null;
    }

    public function sendContract(Request $request)
{
    Log::info("start sending document");
    
    $accessToken = $this->getAccessToken();
    if (!$accessToken) {
        Log::error("error while getting access token");
        return response()->json(["error" => "Failed to get access token"], 401);
    }
    Log::info($accessToken);

    $templateId = "1094000000035451"; 

    $payload = [
        "templates" => [
           "default_fields" => [
                "trainee_name" =>$request->recipient_name,  
                "trainee_address" =>  $request->recipient_email,
            ],
            "notes" => "",
            "actions" => [
                [
                    "recipient_name" => $request->recipient_name,
                    "recipient_email" => $request->recipient_email,
                    "action_id" => "1094000000040028",
                    "signing_order" => 1,
                    "role" => "",
                    "verify_recipient" => false,
                    "private_notes" => "",
                ],
            ],
        ],
    ];

    Log::info("send request without ssl");
    $response = Http::withOptions([
        'verify' => false, // just for test
    ])->withHeaders([
        "Authorization" => "Zoho-oauthtoken " . $accessToken,
        "Content-Type" => "application/json",
    ])->post("https://sign.zoho.sa/api/v1/templates/{$templateId}/createdocument", $payload);

    Log::info("request sent succefully", ['response' => $response->json()]);

    return $response->json();


// $response = Http::withHeaders([
//     "Authorization" => "Zoho-oauthtoken " . $accessToken,
//     "Content-Type" => "application/json",
// ])->post("https://sign.zoho.sa/api/v1/templates/{$templateId}/createdocument", $payload);

}

public function sendEmbeddedContract(Request $request)
{
    Log::info("start sending embedded document");

    $recipientName = $request->recipient_name;
    $recipientEmail = $request->recipient_email;

    $accessToken = $this->getAccessToken();
    if (!$accessToken) {
        Log::error("error while getting access token");
        return response()->json(["error" => "error while getting access token"], 401);
    }

    Log::info("access token successfully generated");

    $templateId = "1094000000035451"; 
    
    $payload = [
        "templates" => [
            "default_fields" => [
                "trainee_name" => $recipientName,  
                "trainee_address" => $recipientEmail,
            ],
            "notes" => "",
            "actions" => [
                [
                    "recipient_name" => $recipientName,
                    "recipient_email" => $recipientEmail,
                    "signing_order" => 1,
                    "role"=> "",
                    "verify_recipient"=> false,
                    "is_embedded" => true,
                    "action_id"=> "1094000000035472",
                    "private_notes" => "",
                ],
            ],
        ],
    ];

    $action_id=1094000000035472;

    $response = Http::withOptions(['verify' => false]) // for test
        ->withHeaders([
            "Authorization" => "Zoho-oauthtoken " . $accessToken,
            "Content-Type" => "application/json",
        ])
        ->post("https://sign.zoho.sa/api/v1/templates/{$templateId}/createdocument?testing=true", $payload);

        $responseData = $response->json();
        Log::info($responseData['requests']['request_id']);

        




    if ($response->successful()) {
        $responseData = $response->json();

        if (!empty($responseData['requests']) && isset($responseData['requests']['request_id'])) {
            $requestId = $responseData['requests']['request_id'];

            if (!empty($responseData['requests']['actions']) && isset($responseData['requests']['actions']['action_id'])) {
                $actionId = $responseData['requests']['actions']['action_id'];

                $embedTokenResponse = Http::withHeaders([
                    "Authorization" => "Zoho-oauthtoken " . $accessToken,
                ])->post("https://sign.zoho.sa/api/v1/requests/{$requestId}/actions/{$actionId}/embedtoken", [
        
                    'host' => 'http://127.0.0.1:8000'
                ]);

                if ($embedTokenResponse->successful()) {
                    $signingUrl = $embedTokenResponse->json()['sign_url'];
                    return response()->json(['sign_url' => $signingUrl]);
                } else {
                    Log::error("error while getting embedded document URL", [
                        'embedTokenResponse' => $embedTokenResponse->json(),
                    ]);
                    return response()->json(["error" => "error while getting embedded document URL"], 500);
                }
            } else {
                Log::error("error while getting action id");
                return response()->json(["error" => "error while getting action id"], 500);
            }
        } else {
            Log::error("error while getting request id");
            return response()->json(["error" => "error while getting request id"], 500);
        }
    } else {
        Log::error("error while creating document", [
            'response' => $response->json(),
            'status' => $response->status(),
        ]);
        return response()->json(["error" => "error while creating document"], 500);
    }
}







    public function test(){
        return Inertia::render('TestZoho');
    }
}
