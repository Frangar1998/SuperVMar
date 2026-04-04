import { TableComponent } from "../../../commons/components/Table/TableComponent.tsx";
import type { TableData } from "../../../commons/components/Table/TableData.ts";
import { useNavigate } from "react-router";
import { useEffect, useState } from "react";
import { JobService } from "../../services/JobService.ts";
import { useSession } from "../../../login/contexts/SessionContext.ts";
import { LoadingComponent } from "../../../commons/components/LoadingComponent.tsx";
import type { JobTable } from "../../types/UserTypes.ts";
import { JOBS_TABLE_HEADERS } from "../../components/Job/JobsTableHeaders.ts";
import { JobsTableRow } from "../../components/Job/JobsTableRow.tsx";
import { AddJobButtonComponent } from "../../components/Job/AddJobButtonComponent.tsx";

export const JobsPage = () => {
    const navigate = useNavigate();
    const [jobs, setJobs] = useState<JobTable[]>([]);
    const [loading, setLoading] = useState(true);
    const { session } = useSession();

    const fetchJobs = async () => {
        try {
            setLoading(true);
            const data = await JobService.getJobs(session);
            setJobs(data);
        } catch (error) {
            console.log(error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchJobs();
    }, []);

    const tableData: TableData<JobTable>[] = jobs.map((job) => ({
        data: {
            id: job.id,
            name: job.name,
        },
    }));

    const handleRowClick = (row: TableData<JobTable>) => {
        navigate(`/usuarios/trabajos/${row.data.id}`);
    };

    if (loading) {
        return <LoadingComponent />;
    }

    return (
        <>
            <div style={{ display: 'flex', justifyContent: 'flex-end', marginBottom: '1rem' }}>
                <AddJobButtonComponent />
            </div>
            <TableComponent<JobTable>
                tableData={tableData}
                initialSortKey={'name'}
                headers={JOBS_TABLE_HEADERS}
                renderRow={JobsTableRow}
                getRowId={(row) => row.data.id}
                onRowClick={handleRowClick}
            />
        </>
    );
};
