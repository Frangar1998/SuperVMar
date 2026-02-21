import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { createBrowserRouter, Navigate, RouterProvider } from "react-router";
import { App } from "./App.tsx";
import { LoginPage } from "./modules/login/pages/LoginPage.tsx";
import './styles.css';
import { DashboardComponent } from "./modules/commons/components/DashboardComponent.tsx";
import { HomePage } from "./modules/home/pages/HomePage.tsx";
import { SupermarketPage } from "./modules/supermarket/pages/SupermarketPage.tsx";
import { ProductsPage } from "./modules/product/pages/Product/ProductsPage.tsx";
import { SalesPage } from "./modules/sale/pages/SalesPage.tsx";
import { UsersPage } from "./modules/user/pages/UsersPage.tsx";
import { CategoriesPage } from "./modules/product/pages/Category/CategoriesPage.tsx";
import { SuppliersPage } from "./modules/product/pages/Supplier/SuppliersPage.tsx";
import { TaxesPage } from "./modules/product/pages/Tax/TaxesPage.tsx";
import { AllocationsPage } from "./modules/product/pages/Allocation/AllocationsPage.tsx";
import { ProductPage } from "./modules/product/pages/Product/ProductPage.tsx";
import { ProductCreatePage } from "./modules/product/pages/Product/ProductCreatePage.tsx";
import { CategoryPage } from "./modules/product/pages/Category/CategoryPage.tsx";
import { CategoryCreatePage } from "./modules/product/pages/Category/CategoryCreatePage.tsx";
import { TaxPage } from "./modules/product/pages/Tax/TaxPage.tsx";
import { TaxCreatePage } from "./modules/product/pages/Tax/TaxCreatePage.tsx";
import { SupplierPage } from "./modules/product/pages/Supplier/SupplierPage.tsx";
import { SupplierCreatePage } from "./modules/product/pages/Supplier/SupplierCreatePage.tsx";

const router = createBrowserRouter([
    {
        Component: App,
        children: [
            {
                path: '/login',
                Component: LoginPage
            },
            {
                path: '/',
                Component: DashboardComponent,
                children: [
                    {
                        path: '/',
                        Component: HomePage,
                    },
                    {
                        path: '/supermercado',
                        Component: SupermarketPage,
                    },
                    {
                        path: '/productos',
                        children: [
                            {
                                index: true,
                                Component: () => <Navigate to="catalogo" replace />
                            },
                            {
                                path: 'catalogo',
                                Component: ProductsPage,
                            },
                            {
                                path: 'nuevo',
                                Component: ProductCreatePage,
                            },
                            {
                                path: ':id',
                                Component: ProductPage,
                            },
                            {
                                path: 'asignaciones',
                                Component: AllocationsPage,
                            },
                            {
                                path: 'categorias',
                                Component: CategoriesPage,
                            },
                            {
                                path: 'categorias/nueva',
                                Component: CategoryCreatePage,
                            },
                            {
                                path: 'categorias/:id',
                                Component: CategoryPage,
                            },
                            {
                                path: 'proveedores',
                                Component: SuppliersPage,
                            },
                            {
                                path: 'proveedores/nuevo',
                                Component: SupplierCreatePage,
                            },
                            {
                                path: 'proveedores/:id',
                                Component: SupplierPage,
                            },
                            {
                                path: 'iva',
                                Component: TaxesPage,
                            },
                            {
                                path: 'iva/nuevo',
                                Component: TaxCreatePage,
                            },
                            {
                                path: 'iva/:id',
                                Component: TaxPage,
                            },
                        ],
                    },
                    {
                        path: '/ventas',
                        Component: SalesPage,
                    },
                    {
                        path: '/usuarios',
                        Component: UsersPage,
                    },
                ],
            },
        ],
    },
]);

createRoot(document.getElementById('root')!).render(
    <StrictMode>
        <RouterProvider router={router} />
    </StrictMode>
);
