import { useContext } from "react";
import { useNavigate } from "react-router-dom";
import { Layout, Page, Text, Box, Banner } from "@shopify/polaris";
import { ShopContext } from "../contexts/ShopContext";
import PlanCard from "../components/plans/PlanCard/PlanCard";

const Plans = () => {
    // Initialize the navigate function from react-router-dom
    const navigate = useNavigate();
    // Get the shop, host, and planData from the context
    const { shop, host, planData } = useContext(ShopContext);
    // Check if the planData is available and if it contains the planName
    const planName = planData?.planName || "";
    // Check if the plan name contains the keyword "developer" or "development"
    const hasKeyword = /developer|development/i.test(planName);

    // Redirect to billing page with planId
    const handleBillingRedirect = (planId) => {
        // Check if shop and host are available
        if (!shop || !host) {
            // Show an alert
            alert("Shop or Host missing!");
            return;
        }

        // Redirect to the billing page with the selected planId
        window.location.href = `/billing/${planId}?shop=${shop}&host=${host}`;
    };


    console.log("Plan Data:", planData);
    console.log("shop:", shop);
    console.log("host:", host);

    return (
        <Page
            title="Choose your plan"
            backAction={{
                content: "Back",
                /* On click redirect to the previous page */
                onAction: () => navigate(-1),
            }}
        >
            <Box paddingInline="300">
                <Layout>
                    <Layout.Section variant="oneHalf">
                        <PlanCard
                            title="All in One Yearly"
                            price="$7.99 /month (billed annually)"
                            planId={1}
                            activePlanId={planData?.charge?.plan_id}
                            isActive={
                                planData?.charge?.status === "ACTIVE" &&
                                planData?.charge?.plan_id === 1
                            }
                            // Onclick redirect to billing page
                            onClick={() => handleBillingRedirect(1)}
                            ctaText={
                                planData?.charge?.plan_id === 2
                                    ? "Upgrade & Save 20%"
                                    : "Try 3 days free"
                            }
                            customFirstFeature={
                                "Save 20% yearly. No extra charges are applied."
                            }
                        />
                    </Layout.Section>

                    <Layout.Section variant="oneHalf">
                        <PlanCard
                            title="All in One Monthly"
                            price="$9.99 /month"
                            planId={2}
                            activePlanId={planData?.charge?.plan_id}
                            isActive={
                                planData?.charge?.status === "ACTIVE" &&
                                planData?.charge?.plan_id === 2
                            }
                            // Onclick redirect to billing page
                            onClick={() => handleBillingRedirect(2)}
                            ctaText={
                                planData?.charge?.plan_id === 1
                                    ? "Downgrade"
                                    : "Try 3 days free"
                            }
                            customFirstFeature="Easy monthly plan. No extra charges are applied."
                        />
                    </Layout.Section>
                </Layout>

                {/* Show a banner if the plan is active or if there is no active subscription */}
                {/* If the plan is active, show a warning banner to cancel the subscription */}
                {planData?.charge?.status === "ACTIVE" ? (
                    <Box paddingBlockStart={400}>
                        <Banner title="Cancel your subscription" tone="warning">
                            <Text>
                                Uninstalling the app will automatically cancel
                                your subscription plan.
                            </Text>
                        </Banner>
                    </Box>
                ) : !hasKeyword ? (
                    <Box paddingBlockStart={400}>
                        <Banner title="No active subscription" tone="warning">
                            <Text>
                                Please choose a plan to create the rule.
                            </Text>
                        </Banner>
                    </Box>
                ) : null}
            </Box>
        </Page>
    );
};

export default Plans;
