<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\AppUser;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Osiset\ShopifyApp\Storage\Models\Charge;
use App\Models\User;

class appUserController extends Controller
{
    public function storeUserData(Request $request)
    {

        $validated = $request->validate([
            'shop' => 'required|string',
            'clientId' => 'required|string',
            'clientSecret' => 'required|string',
            'sessionToken' => 'required|string',
        ]);

        $url = "https://{$validated['shop']}/admin/oauth/access_token";

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

        if ($appUser->last_session && $appUser->last_session !== $originalResponse['session']) {


            $shop = Auth::user();
            $chargeResult = Charge::where('user_id', $shop->id)->first();

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




            $appUser->first_name = $userData['first_name'] ?? null;
            $appUser->last_name = $userData['last_name'] ?? null;
            $appUser->email = $userData['email'] ?? null;
            $appUser->email_verified = $userData['email_verified'] ?? false;
            $appUser->account_owner = $userData['account_owner'] ?? false;
            $appUser->collaborator = $userData['collaborator'] ?? false;
            $appUser->locale = $userData['locale'] ?? null;
            $appUser->last_login_time = Carbon::now();
            $appUser->store_url = $shopData->url;
            $appUser->store_name = $shopData->name;
            $appUser->last_session = $originalResponse['session'];
            $appUser->myshopify_store_url = $shopData->myshopifyDomain;
            $appUser->app_name = json_encode($request->appName);
            $appUser->plan_status = $chargeResult->status ?? null;
            $appUser->plan_name = $chargeResult->name ?? null;
            $appUser->plan_price = $chargeResult->price ?? null;
            $appUser->plan_activation_time = $chargeResult->activated_on ?? null;

            $appUser->save();






            return response()->json([
                'message' => 'User stored successfully',
                'user' => $appUser
            ]);
        } else {
            return response()->json([
                'message' => 'User already exists with the same session',
                'user' => $appUser
            ]);
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
