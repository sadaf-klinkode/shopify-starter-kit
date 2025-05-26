<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\AppUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class appUserController extends Controller
{
    public function getUserData(Request $request)
    {
        $shop = Auth::user();

        $shopQuery = '
        {
            shop {
                name
                myshopifyDomain
                url
            }
        }';

        // Execute the GraphQL query via Shopify API
        $shopData = $shop->api()->graph($shopQuery);
        $shopData = $shopData['body']['data']['shop'];

        // dd($shopData);

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

            $data = $response->json();
            $originalResponse = $response->json();


            if (!$response->successful() || !isset($data['associated_user'])) {
                return response()->json([
                    'error' => 'Invalid Shopify response',
                    'data' => $data
                ], $response->status());
            }

            $userData = $data['associated_user'];

            // Fetch or create the AppUser
            $appUser = AppUser::firstOrNew([
                'user_id' => $userData['id']
            ]);

            // Update fields (apps left blank for now)
            $appUser->first_name = $userData['first_name'] ?? null;
            $appUser->last_name = $userData['last_name'] ?? null;
            $appUser->email = $userData['email'] ?? null;
            $appUser->email_verified = $userData['email_verified'] ?? false;
            $appUser->account_owner = $userData['account_owner'] ?? false;
            $appUser->collaborator = $userData['collaborator'] ?? false;
            $appUser->locale = $userData['locale'] ?? null;
            $appUser->last_login_time = Carbon::now();
            $appUser->store_url = $shopData->url; //need to discuss this
            $appUser->store_name = $shopData->name; //need to discuss this
            $appUser->last_session = $originalResponse['session'];
            $appUser->myshopify_store_url = $shopData->myshopifyDomain; //need to discuss this
            $appUser->app_name = json_encode($request->appName); //need to discuss this

            $appUser->save();

            return response()->json([
                'message' => 'User stored successfully',
                'user' => $appUser
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Token exchange failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function getUserAnalytics()
    {
        $users = AppUser::all();

        return response()->json(
            $users
        );




        /* $totalUsers = $users->count();
        $activeUsers = $users->where('last_login_time', '>=', Carbon::now()->subDays(30))->count();
        $inactiveUsers = $totalUsers - $activeUsers;
        $newUsers = $users->where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $averageSessionDuration = $users->avg(function ($user) {
            return $user->last_session ? Carbon::parse($user->last_session)->diffInMinutes(Carbon::now()) : 0;
        });
        $mostActiveUser = $users->sortByDesc('last_login_time')->first();
        $mostActiveUserName = $mostActiveUser ? $mostActiveUser->first_name . ' ' . $mostActiveUser->last_name : 'N/A';
        $mostActiveUserEmail = $mostActiveUser ? $mostActiveUser->email : 'N/A';
        $mostActiveUserLastLogin = $mostActiveUser ? $mostActiveUser->last_login_time : 'N/A';
        return response()->json([
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'inactive_users' => $inactiveUsers,
            'new_users' => $newUsers,
            'average_session_duration' => $averageSessionDuration,
            'most_active_user' => [
                'name' => $mostActiveUserName,
                'email' => $mostActiveUserEmail,
                'last_login' => $mostActiveUserLastLogin
            ]
        ]); */
    }
}
