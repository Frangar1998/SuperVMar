import { TableComponent } from "../../../commons/components/Table/TableComponent.tsx";
import type { TableData } from "../../../commons/components/Table/TableData.ts";
import { useNavigate } from "react-router";
import { useEffect, useState } from "react";
import { UserService } from "../../services/UserService.ts";
import { useSession } from "../../../login/contexts/SessionContext.ts";
import { LoadingComponent } from "../../../commons/components/LoadingComponent.tsx";
import type { User, UserTable } from "../../types/UserTypes.ts";
import { USERS_TABLE_HEADERS } from "../../components/User/UsersTableHeaders.ts";
import { UsersTableRow } from "../../components/User/UsersTableRow.tsx";
import { AddUserButtonComponent } from "../../components/User/AddUserButtonComponent.tsx";

export const UsersListPage = () => {
    const navigate = useNavigate();
    const [users, setUsers] = useState<User[]>([]);
    const [loading, setLoading] = useState(true);
    const { session } = useSession();

    const fetchUsers = async () => {
        try {
            setLoading(true);
            const data = await UserService.getUsers(session);
            setUsers(data);
        } catch (error) {
            console.log(error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchUsers();
    }, []);

    const tableData: TableData<UserTable>[] = users.map((user) => ({
        data: {
            id: user.id,
            username: user.username,
            name: user.userData.name,
            surname: user.userData.surname,
            email: user.userData.email,
            isAdmin: user.isAdmin,
            job: user.allocations.map(a => a.job.name).join(', ') || '-',
        },
    }));

    const handleRowClick = (row: TableData<UserTable>) => {
        navigate(`/usuarios/lista/${row.data.id}`);
    };

    if (loading) {
        return <LoadingComponent />;
    }

    return (
        <>
            <div style={{ display: 'flex', justifyContent: 'flex-end', marginBottom: '1rem' }}>
                <AddUserButtonComponent />
            </div>
            <TableComponent<UserTable>
                tableData={tableData}
                initialSortKey={'username'}
                headers={USERS_TABLE_HEADERS}
                renderRow={UsersTableRow}
                getRowId={(row) => row.data.id}
                onRowClick={handleRowClick}
            />
        </>
    );
};
