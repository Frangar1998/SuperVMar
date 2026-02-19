import { TableComponent } from "../../../commons/components/Table/TableComponent.tsx";
import type { TableData } from "../../../commons/components/Table/TableData.ts";
import { useNavigate } from "react-router";
import { useEffect, useState } from "react";
import { TaxService } from "../../services/TaxService.ts";
import { useSession } from "../../../login/contexts/SessionContext.ts";
import { LoadingComponent } from "../../../commons/components/LoadingComponent.tsx";
import type { TaxTable } from "../../types/ProductTypes.ts";
import { TAXES_TABLE_HEADERS } from "../../components/Tax/TaxesTableHeaders.ts";
import { TaxesTableRow } from "../../components/Tax/TaxesTableRow.tsx";
import { AddTaxButtonComponent } from "../../components/Tax/AddTaxButtonComponent.tsx";

export const TaxesPage = () => {
    const navigate = useNavigate();
    const [taxes, setTaxes] = useState<TaxTable[]>([]);
    const [loading, setLoading] = useState(true);
    const { session } = useSession();

    const fetchTaxes = async () => {
        try {
            setLoading(true);
            const data = await TaxService.getTaxes(session);
            setTaxes(data);
        } catch (error) {
            console.log(error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchTaxes();
    }, []);

    const tableData: TableData<TaxTable>[] = taxes.map((tax) => ({
        data: {
            id: tax.id,
            name: tax.name,
            percent: tax.percent,
        },
    }));

    const handleRowClick = (row: TableData<TaxTable>) => {
        navigate(`/productos/iva/${row.data.id}`);
    };

    if (loading) {
        return <LoadingComponent />;
    }

    return (
        <>
            <div style={{ display: 'flex', justifyContent: 'flex-end', marginBottom: '1rem' }}>
                <AddTaxButtonComponent />
            </div>
            <TableComponent<TaxTable>
                tableData={tableData}
                initialSortKey={'name'}
                headers={TAXES_TABLE_HEADERS}
                renderRow={TaxesTableRow}
                getRowId={(row) => row.data.id}
                onRowClick={handleRowClick}
            />
        </>
    );
};