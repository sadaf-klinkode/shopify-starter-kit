<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Osiset\ShopifyApp\Storage\Models\Plan;
use Osiset\ShopifyApp\Services\ChargeHelper;

class PlanController extends Controller
{
    public function getPlanData()
    {
        // Get the currently authenticated shop
        $shop = Auth::user();

        // Get the shop's myshopify domain (store identifier)
        $shopifyDomain = $shop->name;

        // GraphQL query to get the shop's current plan and primary domain
        $shopQuery = '
        {
            shop {
                plan {
                    displayName
                    partnerDevelopment
                    shopifyPlus
                }
                primaryDomain {
                    host
                }
            }
        }';

        // Execute the GraphQL query via Shopify API
        $shopData = $shop->api()->graph($shopQuery);

        // Extract the plan name from the API response
        $planName = $shopData['body']->container['data']['shop']['plan']['displayName'] ?? null;
        // $planName = "Shopify Plus";

        // Extract the shop's primary website domain from the API response
        $website = $shopData['body']->container['data']['shop']['primaryDomain']['host'] ?? null;

        // Get all app subscription plans from the local database
        $plans = Plan::all();

        // Instantiate the ChargeHelper service
        $chargeHelper = app()->make(ChargeHelper::class);

        // Initialize charge as null
        $charge = null;

        // If the shop has a current plan, retrieve the associated charge info
        if ($shop->plan) {
            $charge = $chargeHelper->chargeForPlan($shop->plan->getId(), $shop);
        }

        // Return a JSON response with all relevant data
        return response()->json([
            'plans' => $plans,
            'charge' => $charge,
            'planName' => $planName,
            'shopifyDomain' => $shopifyDomain,
            'website' => $website,
            'success' => true,
        ]);
    }
}
