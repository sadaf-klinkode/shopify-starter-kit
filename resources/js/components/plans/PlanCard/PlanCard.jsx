import { Card, Box, Button, InlineStack, Text, List, Badge } from "@shopify/polaris";

const PlanCard = ({
    title,
    price,
    planId,
    activePlanId,
    isActive,
    onClick,
    ctaText,
    customFirstFeature,
}) => {
      // Shared features for both plans
    const sharedFeatures = [
        "Unlimited orders.",
        "Scheduled discounts.",
        "Multiple conditions in a single rule.",
        "Most quick and straightforward settings.",
        "Order, product & shipping discounts - all 3 types discounts in a single rule.",
        "Discounts based on customer tags, quantity, products, collections, and more.",
        "Daily support, every day of the week.",
    ];
    return (
        <Card>
            <Box paddingBlockEnd="200">
                <Text variant="headingMd" as="h5">
                    {title} {isActive && <Badge tone="success">Active</Badge>}
                </Text>
                <Text as="p" variant="bodySm">
                    {price}
                </Text>
            </Box>

            <Box paddingBlockEnd="200">
                <List type="bullet">
                    <List.Item>{customFirstFeature}</List.Item>
                    {sharedFeatures.map((item, i) => (
                        <List.Item key={i}>{item}</List.Item>
                    ))}
                </List>
            </Box>

            <InlineStack align="end">
                {!isActive ? (
                    <Button variant="primary" onClick={onClick}>
                        {ctaText}
                    </Button>
                ) : activePlanId !== planId ? (
                    <Button variant="primary" onClick={onClick}>
                        {ctaText}
                    </Button>
                ) : (
                    <Button variant="plain"></Button>
                )}
            </InlineStack>
        </Card>
    );
};

export default PlanCard;
