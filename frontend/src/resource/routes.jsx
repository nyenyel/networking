import { createBrowserRouter, Navigate } from "react-router-dom";
import PublicModule from "../module/PublicModule";
import ErrorPage from "../component/ErrorPage";
import HomeOutlet from "../outlet/HomeOutlet";
import ServiceOutlet from "../outlet/ServiceOutlet";
import LoginModule from "../module/LoginModule";
import UserModule from "../module/UserModule";
import UserHomeOutlet from "../outlet/UserHomeOutlet";
import GenealogyOutlet from "../outlet/GenealogyOutlet";
import TransactionOutlet from "../outlet/TransactionOutlet";
import AdminModule from "../module/AdminModule";
import DasboardOutlet from "../outlet/admin/DasboardOutlet";
import MemberOutlet from "../outlet/admin/MemberOutlet";

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
                path: 'home/transaction',
                element: <TransactionOutlet />,
                errorElement: <ErrorPage />
            },
            {
                path: 'genealogy',
                element: <GenealogyOutlet />,
                errorElement: <ErrorPage />
            },
            {
                path: 'admin',
                element: <AdminModule />,
                errorElement: <ErrorPage />,
                children: [
                    {
                        path: 'dashboard',
                        element: <DasboardOutlet />,
                        errorElement: <ErrorPage />
                    },
                    {
                        path: 'member',
                        element: <MemberOutlet />,
                        errorElement: <ErrorPage />
                    },
                    {
                        index:true,
                        element: <Navigate to={'dashboard'} replace />
                    }
                ]
            },
            {
                index:true,
                element: <Navigate to={'home'} replace />
            }
          ]
    },
])