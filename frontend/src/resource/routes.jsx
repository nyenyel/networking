import { createBrowserRouter, Navigate } from "react-router-dom";
import PublicModule from "../module/PublicModule";
import ErrorPage from "../component/ErrorPage";
import HomeOutlet from "../outlet/HomeOutlet";
import ServiceOutlet from "../outlet/ServiceOutlet";
import LoginModule from "../module/LoginModule";
import UserModule from "../module/UserModule";
import UserHomeOutlet from "../outlet/UserHomeOutlet";
import GenealogyOutlet from "../outlet/GenealogyOutlet";

export const router = createBrowserRouter([
    {
      path: '/',
      element: <PublicModule />,
      errorElement: <ErrorPage />,
      children: [
        {
            path: 'home',
            element: <HomeOutlet />,
            errorElement: <ErrorPage />
        },
        {
            path: 'service',
            element: <ServiceOutlet />,
            errorElement: <ErrorPage />
        },
        {
            index:true,
            element: <Navigate to={'home'} replace />
        }
      ]
    },
    {
        path:'login',
        element: <LoginModule/>,
        errorElement: <ErrorPage />
    },
    {
        path:'sku',
        element: <UserModule/>,
        errorElement: <ErrorPage />,
        children: [
            {
                path: 'home',
                element: <UserHomeOutlet />,
                errorElement: <ErrorPage />
            },
            {
                path: 'genealogy',
                element: <GenealogyOutlet />,
                errorElement: <ErrorPage />
            },
            {
                index:true,
                element: <Navigate to={'home'} replace />
            }
          ]
    },
])