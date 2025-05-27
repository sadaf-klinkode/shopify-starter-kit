// resources/js/contexts/ShopContext.jsx
import { createContext, useEffect, useState } from "react";
import { getPlanData } from "../api";

export const ShopContext = createContext();

const ShopProvider = ({ children }) => {
    const [shop, setShop] = useState('test');
    const [host, setHost] = useState('test');
    const [planData, setPlanData] = useState({});
    const [loadingContext, setLoadingContext] = useState(true);

    const fetchPlanData = async () => {
        try {
            const data = await getPlanData().finally(() => {
                setLoadingContext(false);
            });
            setPlanData(data);
        } catch (error) {
            console.error("Failed to fetch plan data:", error);
        }
    };

    useEffect(() => {
        const params = new URLSearchParams(window.location.search);
        const shopParam = params.get("shop");
        const hostParam = params.get("host");

        if (shopParam && hostParam) {
            localStorage.setItem("shop", shopParam);
            localStorage.setItem("host", hostParam);
            setShop(shopParam);
            setHost(hostParam);
        } else {
            // Fallback from localStorage if params not available
            const storedShop = localStorage.getItem("shop");
            const storedHost = localStorage.getItem("host");
            if (storedShop && storedHost) {
                setShop(storedShop);
                setHost(storedHost);
            }
        }

        fetchPlanData();
    }, []);

    const shopInfo = {
        shop,
        host,
        planData,
        loadingContext,
    };
    return (
        <ShopContext.Provider value={shopInfo}>{children}</ShopContext.Provider>
    );
};

export default ShopProvider;
