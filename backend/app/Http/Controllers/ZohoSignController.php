<?php

namespace App\Http\Controllers;

use App\Mail\ContractSigned;
use App\Models\Back\Trainee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;



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

    $templateId = "1094000000056767"; 

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
                    "action_id" => "1094000000056788",
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

    //get trainee details
    $trainee=Trainee::where('user_id',$request->user_id)->first();
    $contractValue = $trainee->override_training_costs ?? 2300;

    $numbersInArabic = [
        2300 => 'ألفان وثلاثمائة',
        2700 => 'ألفان وسبعمائة',
        2900 => 'ألفان وتسعمائة',
        3300 => 'ثلاثة آلاف وثلاثمائة',
        3750 => 'ثلاثة آلاف وسبعمائة وخمسون',
        3800 => 'ثلاثة آلاف وثمانمائة',
        4300 => 'أربعة آلاف وثلاثمائة'
    ];

    $contractValueArabic = $numbersInArabic[$contractValue];





    $accessToken = $this->getAccessToken();
    if (!$accessToken) {
        Log::error("error while getting access token");
        return response()->json(["error" => "error while getting access token"], 401);
    }

    Log::info("access token successfully generated");

    $templateId = "1094000000175325"; 
    
    $payload = [
        "templates" => [
            "default_fields" => [
                "trainee_name" => $recipientName,  
                "trainee_email" => $recipientEmail,
                 "trainee_phone" => $trainee->phone,
                 "trainee_second_phone" =>$trainee->phone_additional,
                 "trainee_identity" => $trainee->identity_number,
                 'contract_value' =>$contractValue,

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
                    "action_id"=> "1094000000175346",
                    "private_notes" => "",
                    "allowed_cloud_provider_ids" =>  [130],
                    "language" => "ar",

                ],
            ],
        ],
    ];


    $response = Http::withOptions(['verify' => false]) // for test
        ->withHeaders([
            "Authorization" => "Zoho-oauthtoken " . $accessToken,
            "Content-Type" => "application/json",
        ])
        ->post("https://sign.zoho.sa/api/v1/templates/{$templateId}/createdocument", $payload);

        $responseData = $response->json();
        Log::info("Response from Zoho: " . json_encode($response->json()));

        Log::info($responseData['requests']['request_id']);
        Log::info($responseData['requests']['actions'][0]['action_id']);



    if ($response->successful()) {
        $responseData = $response->json();
        // $trainee->update(['zoho_contract_id' => $responseData['requests']['request_id']]);

        if (!empty($responseData['requests']) && isset($responseData['requests']['request_id'])) {
         
            $requestId = $responseData['requests']['request_id'];

            if (!empty($responseData['requests']['actions']) && isset($responseData['requests']['actions'][0]['action_id'])) {
                $actionId = $responseData['requests']['actions'][0]['action_id'];

                $embedTokenResponse = Http::withHeaders([
                    "Authorization" => "Zoho-oauthtoken " . $accessToken,
                ])->post("https://sign.zoho.sa/api/v1/requests/{$requestId}/actions/{$actionId}/embedtoken", [
        
                    'host' => 'http://127.0.0.1:8000'
                ]);

                if ($embedTokenResponse->successful()) {
                    $trainee->update(['zoho_contract_id' => $responseData['requests']['request_id']]);
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

public function viewContract()
{
    $user = Auth::user();
    $trainee = Trainee::where('user_id', $user->id)->first();

    if (!$trainee || !$trainee->zoho_contract_id) {
        return abort(404, 'Contract not found.');
    }

    $contractId = $trainee->zoho_contract_id;
    Log::info($contractId);

    $accessToken = $this->getAccessToken();
    if (!$accessToken) {
        Log::error("Error while getting access token");
        return response()->json(["error" => "Error while getting access token"], 401);
    }

    $response = Http::withHeaders([
        "Authorization" => "Zoho-oauthtoken " . $accessToken,
    ])->get("https://sign.zoho.sa/api/v1/requests/{$contractId}/pdf");

    if ($response->failed()) {
        Log::error("Failed to fetch contract from Zoho: " . $response->body());
        return abort(500, 'Failed to fetch contract.');


    }

    return response()->json([
        'pdf_url' => 'data:application/pdf;base64,' . base64_encode($response->body())
    ]);
}


public function checkContractStatus()
{
    Log::info('Checking contract status...');

    $user = Auth::user();
    $trainee = Trainee::where('user_id', $user->id)->first();

    if (!$trainee || !$trainee->zoho_contract_id) {
        Log::info("No contract found for user {$user->id}. Contract has not been sent yet.");
        return response()->json(["status" => "contract_not_sent"]);
    }

    if ($trainee->zoho_contract_status === 'completed' && $trainee->zoho_sign_date) {
        Log::info("Contract for user {$user->id} is already completed.");
        return response()->json(["status" => "completed"]);
    }

    $contractId = $trainee->zoho_contract_id;
    $accessToken = $this->getAccessToken();

    if (!$accessToken) {
        Log::error("Failed to get access token for user {$user->id}");
        return response()->json(["error" => "Failed to get access token"], 401);
    }

    $response = Http::withHeaders([
        "Authorization" => "Zoho-oauthtoken " . $accessToken,
    ])->get("https://sign.zoho.sa/api/v1/requests/{$contractId}");

    if ($response->failed()) {
        Log::error("Failed to fetch contract status from Zoho for contract ID {$contractId}. Response: " . $response->body());
        return response()->json(["error" => "Failed to fetch contract status"], 500);
    }

    $status = $response->json()['requests']['request_status'] ?? null;
    $timestamp = $response->json()['requests']['sign_submitted_time'] ?? null;
    if($timestamp){
        $signDate = Carbon::createFromTimestampMs($timestamp)->toDateTimeString();
    }

    if (!$status) {
        Log::error("Invalid response from Zoho for contract ID {$contractId}: Missing request_status.");
        return response()->json(["error" => "Invalid contract status response"], 500);
    }

    if ($trainee->zoho_contract_status !== $status) {
        $trainee->zoho_contract_status = $status;
        $trainee->save();
        Log::info("Updated contract status for user {$user->id} to: {$status}");
    }

    if ($status === 'completed' && !$trainee->contract_signed_notification_sent && !$trainee->zoho_sign_date ) {
        try {
            $trainee->zoho_sign_date=$signDate;
            Mail::to($trainee->email)->send(new ContractSigned($trainee));
            $trainee->contract_signed_notification_sent = true;
            $trainee->save();
            Log::info("Contract signed notification sent to: {$trainee->email}");
        } catch (\Exception $e) {
            Log::error("Failed to send contract signed email to {$trainee->email}: " . $e->getMessage());
        }
    }

    return response()->json(["status" => $status]);
}


public function contractMustSign(Request $request){
    $trainee=Trainee::find($request->trainee_id);
    $trainee->must_sign=true;
    $trainee->save();

   Log::info("contract sent succefully");

   return redirect()->back()->with('success','contract sent succefully');
}





}



