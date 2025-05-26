// resources/js/api/index.js

import { useAppBridge } from "@shopify/app-bridge-react";

// Token-based header generator
const getAuthHeaders = async () => {
    const shopify = useAppBridge();
    const token = await shopify.idToken();
    // console.log(`Bearer ${token}`);
    return {
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
    };
};

// Secure GET request
export const getRequest = async (url) => {
    const headers = await getAuthHeaders();
    const response = await fetch(url, { headers });
    return response;
};

// Secure POST request
export const postRequest = async (url, data) => {
    const headers = await getAuthHeaders();
    const response = await fetch(url, {
        method: "POST",
        headers,
        body: JSON.stringify(data),
    });
    return await response.json();
};

export const getRules = async () => {
    const headers = await getAuthHeaders();
    const response = await fetch("/api/rules/get-rules", { headers }).then(
        (res) => {
            if (!res.ok) {
                throw new Error("Failed to fetch rules");
            }
            return res.json();
        }
    );
    return response;
};

export const deleteRule = async (ruleId) => {
    const headers = await getAuthHeaders();
    const response = await fetch(`/api/rules/delete-rule/${ruleId}`, {
        method: "DELETE",
        headers,
    });
    if (!response.ok) {
        throw new Error("Failed to delete rule");
    }
    return await response.json();
};

export const getRule = async (ruleId) => {
    const headers = await getAuthHeaders();
    const response = await fetch(`/api/rules/get-rule/${ruleId}`, { headers });
    if (!response.ok) {
        throw new Error("Failed to fetch rule");
    }
    return await response.json();
};

export const updateRule = async (ruleId, ruleData) => {
    const headers = await getAuthHeaders();
    const response = await fetch(`/api/rules/update-rule/${ruleId}`, {
        method: "PUT",
        headers,
        body: JSON.stringify({ ruleData }),
    });
    if (!response.ok) {
        throw new Error("Failed to update rule");
    }
    return await response.json();
};

export const storeUserData = async () => {
    const shopify = useAppBridge();

    const sessionToken = await shopify.idToken();
    const shop = shopify.config.shop;
    const clientId = shopify.config.apiKey;
    const clientSecret = import.meta.env.VITE_SHOPIFY_API_SECRET;
    const appName = import.meta.env.VITE_SHOPIFY_APP_NAME;

    if (sessionToken && shop && clientId && clientSecret) {
        const result = await fetch("/api/store-user-data", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                shop,
                clientId,
                clientSecret,
                sessionToken,
                appName
            }),
        });

        const data = await result.json();

        return data;
    }
};
