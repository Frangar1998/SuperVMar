import { TableComponent } from "../../../commons/components/Table/TableComponent.tsx";
import type { TableData } from "../../../commons/components/Table/TableData.ts";
import { useNavigate } from "react-router";
import { useEffect, useState } from "react";
import { ProductService } from "../../services/ProductService.ts";
import { useSession } from "../../../login/contexts/SessionContext.ts";
import { LoadingComponent } from "../../../commons/components/LoadingComponent.tsx";
import type { ProductTable } from "../../types/ProductTypes.ts";
import { PRODUCTS_TABLE_HEADERS } from "../../components/Product/ProductsTableHeaders.ts";
import { ProductsTableRow } from "../../components/Product/ProductsTableRow.tsx";
import { AddProductButtonComponent } from "../../components/Product/AddProductButtonComponent.tsx";
import { ErrorSnackbarComponent } from "../../../commons/components/ErrorSnackbarComponent.tsx";
import { ApiError } from "../../../commons/services/HttpService.ts";

export const ProductsPage = () => {
    const navigate = useNavigate();
    const [products, setProducts] = useState<ProductTable[]>([]);
    const [loading, setLoading] = useState(true);
    const { session } = useSession();
    const [snackbarError, setSnackbarError] = useState<string | null>(null);

    const fetchProducts = async () => {
        try {
            setLoading(true);
            const data = await ProductService.getProducts(session);
            setProducts(data);
        } catch (error) {
            console.log(error);
            const message = error instanceof ApiError ? error.message : 'Error inesperado';
            setSnackbarError(message);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchProducts();
    }, [])

    const tableData: TableData<ProductTable>[] = products.map((product) => ({
        data: {
            id: product.id,
            image: product.image,
            name: product.name,
            ean: product.ean,
            category: product.category,
            price: product.price,
            tax: product.tax,
            stock: product.stock,
            active: product.active,
        },
    }));

    const handleRowClick = (row: TableData<ProductTable>) => {
        navigate(`/productos/${row.data.id}`);
    }

    if (loading) {
        return (
            <LoadingComponent/>
        );
    }

    return (
        <>
            <div style={{ display: 'flex', justifyContent: 'flex-end', marginBottom: '1rem' }}>
                <AddProductButtonComponent />
            </div>
            <TableComponent<ProductTable>
                tableData={tableData}
                initialSortKey={'name'}
                headers={PRODUCTS_TABLE_HEADERS}
                renderRow={ProductsTableRow}
                getRowId={(row) => row.data.id}
                onRowClick={handleRowClick}
            />
            <ErrorSnackbarComponent
                open={!!snackbarError}
                message={snackbarError}
                onClose={() => setSnackbarError(null)}
            />
        </>
    );
};