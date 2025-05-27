// resources/js/pages/Home.jsx

import { useEffect, useState } from "react";
import { getRules, deleteRule } from "../api";
import { Page, Card, EmptyState } from "@shopify/polaris";
import RulesTable from "../components/RulesTable";
import { useNavigate } from "react-router-dom";
import { storeUserData } from "../api";
import { useContext } from "react";
import { ShopContext } from "../contexts/ShopContext";

const Home = () => {
    const navigate = useNavigate();
    const [rules, setRules] = useState([]);
    const [loading, setLoading] = useState(true);
    const { loadingContext } = useContext(ShopContext);

    const handleDelete = async (ruleId) => {
        if (window.confirm("Are you sure you want to delete this rule?")) {
            const id = ruleId.split("/").pop();
            const response = await deleteRule(id);

            if (response.success) {
                setRules((prevRules) =>
                    prevRules.filter((rule) => rule.node.id !== ruleId)
                );
                alert("Rule deleted successfully");
            } else {
                alert("Failed to delete rule");
            }
        }
    };

    const fetchUserData = async () => {
        try {
            const userData = await storeUserData();
            return userData;
        } catch (error) {
            console.error("Error fetching user data:", error);
            return null;
        }
    }

    useEffect(() => {
        const fetchRules = async () => {
            const response = await getRules("/api/rules/get-rules");
            if (!response.success) {
                console.error("Failed to fetch rules");
                setLoading(false);
                return;
            }

            // const user = await shopify.user();

            // console.log('user: ',user);

            setRules(response.rules);
            setLoading(false);
        };

        // fetchUserData();
        fetchRules();
    }, []);

    if (loadingContext) {
        return <div>Loading...</div>;
    }

    return (
        <Page
            title="Discount App"
            primaryAction={{
                content: "Create discount",
                onAction: () => navigate("/rules/create"),
            }}
        >
            {rules.length === 0 && !loading ? (
                <Card sectioned>
                    <EmptyState
                        heading="No discount rules. Create one!"
                        image="https://cdn.shopify.com/s/files/1/0262/4071/2726/files/emptystate-files.png"
                        fullWidth
                        action={{
                            content: "Create discount",
                            onAction: () => navigate("/rules/create"),
                        }}
                    >
                        <p>
                            Add discount codes and automatic discounts that
                            apply at checkout.
                        </p>
                    </EmptyState>
                </Card>
            ) : loading ? (
                <div>Loading...</div>
            ) : (
                <RulesTable rules={rules} handleDelete={handleDelete} />
            )}
        </Page>
    );
};

export default Home;
