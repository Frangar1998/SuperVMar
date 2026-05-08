import type { UserTableHeader, UserTable } from "../../types/UserTypes.ts";

export const USERS_TABLE_HEADERS: UserTableHeader[] = [
    { id: 'username' as keyof UserTable, numeric: false, label: 'Usuario' },
    { id: 'name' as keyof UserTable, numeric: false, label: 'Nombre' },
    { id: 'surname' as keyof UserTable, numeric: false, label: 'Apellidos' },
    { id: 'email' as keyof UserTable, numeric: false, label: 'Email' },
    { id: 'job' as keyof UserTable, numeric: false, label: 'Trabajo' },
];
