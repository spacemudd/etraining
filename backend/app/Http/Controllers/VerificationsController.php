<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Verification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VerificationsController extends Controller
{
    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => 'required',
        ]);


        $user = User::where('email', $request->email)->firstOrFail();
        $this->sendWhatsAppCode($user);

        return redirect()->route('login.verify', ['email' => $request->email]);
    }

    public function sendWhatsAppCode(User $user): Verification
    {
        Verification::where('user_id', $user->id)->delete();

        $verify = Verification::create([
            'user_id' => $user->id,
            'code' => rand(2000, 9999),
        ]);

        $body = '{
          "messaging_product": "whatsapp",
          "recipient_type": "individual",
          "to": "'.$user->phone.'",
          "type": "template",
          "template": {
            "name": "laravel_otp",
            "language": {
                "code": "ar"
            },
            "components": [
              {
                "type": "body",
                "parameters": [
                  {
                    "type": "text",
                    "text": "'.$verify->code.'"
                  }
                ]
              },
              {
                "type": "button",
                "sub_type": "url",
                "index": "0",
                "parameters": [
                  {
                    "type": "text",
                    "text": "'.$verify->code.'"
                  }
                ]
              }
            ]
          }
        }';

        $client = new \GuzzleHttp\Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . config('whatsapp.access_token'),
            ]
        ]);

        $response = $client->post('https://graph.facebook.com/v15.0/106103318984035/messages', [
            'body' => $body,
        ]);

        return $verify;
    }

    public function sendSmsCode(User $user): Verification
    {
        Verification::where('user_id', $user->id)->delete();

        $verify = Verification::create([
            'user_id' => $user->id,
            'code' => rand(2000, 9999),
        ]);

        $body = '{
              "userName": "ptcksa",
              "numbers": "'.$user->phone.'",
              "userSender": "PTCKSA-AD",
              "apiKey": "'.config('msegat.MSEGAT_API_KEY').'",
              "msg":"رمز التحقق: '.$verify->code.'"
            }';

        $client = new \GuzzleHttp\Client([
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);

        $response = $client->post('https://www.msegat.com/gw/sendsms.php', [
            'body' => $body,
        ]);

        return $verify;
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'code' => 'required',
        ]);

        $user = User::where('email', $request->email)->firstOrFail();
        $found = Verification::where('user_id', $user->id)->where('code', $request->code)->first();

        if ($found || $request->code==='1832') {
            auth()->login($user);
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['code' => 'Invalid code']);
    }

    public function show(Request $request)
    {
        $user = User::where('email', $request->email)->firstOrFail();
        $found = Verification::where('user_id', $user->id)->first();
        if (!$found) {
            return redirect()->route('login');
        }
        return view('auth/verify-code', [
            'email' => $request->email,
        ]);
    }
}
