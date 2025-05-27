import { Outlet } from "react-router-dom";
import { NavMenu } from "@shopify/app-bridge-react";
import { Link } from "react-router-dom";
const MainLayout = () => {
    return (
        <>
            <NavMenu>
                <Link to="/" rel="home">
                    Home
                </Link>
                <Link to="/rules/create">Create</Link>
                <Link to="/plans">Plans</Link>
            </NavMenu>
            <Outlet />
        </>
    );
};

export default MainLayout;
