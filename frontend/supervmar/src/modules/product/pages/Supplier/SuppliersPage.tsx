import { TableComponent } from "../../../commons/components/Table/TableComponent.tsx";
import type { TableData } from "../../../commons/components/Table/TableData.ts";
import { useNavigate } from "react-router";
import { useEffect, useState } from "react";
import { SupplierService } from "../../services/SupplierService.ts";
import { useSession } from "../../../login/contexts/SessionContext.ts";
import { LoadingComponent } from "../../../commons/components/LoadingComponent.tsx";
import type { SupplierTable } from "../../types/ProductTypes.ts";
import { SUPPLIERS_TABLE_HEADERS } from "../../components/Supplier/SuppliersTableHeaders.ts";
import { SuppliersTableRow } from "../../components/Supplier/SuppliersTableRow.tsx";
import { AddSupplierButtonComponent } from "../../components/Supplier/AddSupplierButtonComponent.tsx";

export const SuppliersPage = () => {
    const navigate = useNavigate();
    const [suppliers, setSuppliers] = useState<SupplierTable[]>([]);
    const [loading, setLoading] = useState(true);
    const { session } = useSession();

    const fetchSuppliers = async () => {
        try {
            setLoading(true);
            const data = await SupplierService.getSuppliers(session);
            setSuppliers(data);
        } catch (error) {
            console.log(error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchSuppliers();
    }, []);

    const tableData: TableData<SupplierTable>[] = suppliers.map((supplier) => ({
        data: {
            id: supplier.id,
            name: supplier.name,
            phone: supplier.phone,
            email: supplier.email,
            contact: supplier.contact,
        },
    }));

    const handleRowClick = (row: TableData<SupplierTable>) => {
        navigate(`/productos/proveedores/${row.data.id}`);
    };

    if (loading) {
        return <LoadingComponent />;
    }

    return (
        <>
            <div style={{ display: 'flex', justifyContent: 'flex-end', marginBottom: '1rem' }}>
                <AddSupplierButtonComponent />
            </div>
            <TableComponent<SupplierTable>
                tableData={tableData}
                initialSortKey={'name'}
                headers={SUPPLIERS_TABLE_HEADERS}
                renderRow={SuppliersTableRow}
                getRowId={(row) => row.data.id}
                onRowClick={handleRowClick}
            />
        </>
    );
};