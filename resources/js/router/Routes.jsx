
// resources/js/router/Routes.jsx
import { createBrowserRouter } from "react-router-dom";
import MainLayout from "../components/partials/MainLayout/MainLayout";
import Home from "../pages/Home";
import Create from "../pages/Create";
import Edit from "../pages/Edit";
import Plans from "../pages/Plans";

const router = createBrowserRouter([
    {
        path: '/',
        element: <MainLayout />,
        children: [
            {
                index: true,
                element: <Home />
            },
            {
                path: '/rules/create',
                element: <Create />
            },
            {
                path: '/rules/edit/:id',
                element: <Edit />
            },
            {
                path: '/plans',
                element: <Plans />
            }
        ]
    }

]);

export default router;