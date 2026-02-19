import { TableComponent } from "../../../commons/components/Table/TableComponent.tsx";
import type { TableData } from "../../../commons/components/Table/TableData.ts";
import { useNavigate } from "react-router";
import { useEffect, useState } from "react";
import { CategoryService } from "../../services/CategoryService.ts";
import { useSession } from "../../../login/contexts/SessionContext.ts";
import { LoadingComponent } from "../../../commons/components/LoadingComponent.tsx";
import type { CategoryTable } from "../../types/ProductTypes.ts";
import { CATEGORIES_TABLE_HEADERS } from "../../components/Category/CategoriesTableHeaders.ts";
import { CategoriesTableRow } from "../../components/Category/CategoriesTableRow.tsx";
import { AddCategoryButtonComponent } from "../../components/Category/AddCategoryButtonComponent.tsx";

export const CategoriesPage = () => {
    const navigate = useNavigate();
    const [categories, setCategories] = useState<CategoryTable[]>([]);
    const [loading, setLoading] = useState(true);
    const { session } = useSession();

    const fetchCategories = async () => {
        try {
            setLoading(true);
            const data = await CategoryService.getCategories(session);
            setCategories(data);
        } catch (error) {
            console.log(error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchCategories();
    }, []);

    const tableData: TableData<CategoryTable>[] = categories.map((category) => ({
        data: {
            id: category.id,
            name: category.name,
        },
    }));

    const handleRowClick = (row: TableData<CategoryTable>) => {
        navigate(`/productos/categorias/${row.data.id}`);
    };

    if (loading) {
        return <LoadingComponent />;
    }

    return (
        <>
            <div style={{ display: 'flex', justifyContent: 'flex-end', marginBottom: '1rem' }}>
                <AddCategoryButtonComponent />
            </div>
            <TableComponent<CategoryTable>
                tableData={tableData}
                initialSortKey={'name'}
                headers={CATEGORIES_TABLE_HEADERS}
                renderRow={CategoriesTableRow}
                getRowId={(row) => row.data.id}
                onRowClick={handleRowClick}
            />
        </>
    );
};