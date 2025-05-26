<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    public function getUserData(Request $request)
    {
        $validated = $request->validate([
            'shop' => 'required|string',
            'clientId' => 'required|string',
            'clientSecret' => 'required|string',
            'sessionToken' => 'required|string',
        ]);

        $url = "https://{$validated['shop']}/admin/oauth/access_token";

        try {
            $response = Http::asJson()->post($url, [
                'client_id' => $validated['clientId'],
                'client_secret' => $validated['clientSecret'],
                'grant_type' => 'urn:ietf:params:oauth:grant-type:token-exchange',
                'subject_token' => $validated['sessionToken'],
                'subject_token_type' => 'urn:ietf:params:oauth:token-type:id_token',
                'requested_token_type' => 'urn:shopify:params:oauth:token-type:online-access-token'
            ]);

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Token exchange failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
