export interface Job {
    id: string;
    name: string;
}

export interface JobTable {
    id: string;
    name: string;
}

export interface JobTableHeader {
    id: keyof JobTable;
    numeric: boolean;
    label: string;
}

export interface UserAddress {
    id: string;
    name: string;
    postalCode: string;
    city: string;
    number: string;
    province: string;
    floor: string | null;
    door: string | null;
    other: string | null;
}

export interface UserData {
    id: string;
    name: string;
    surname: string;
    email: string;
    phone: string;
    address: UserAddress;
}

export interface UserAllocation {
    supermarket: { id: string; name: string };
    job: { id: string; name: string };
}

export interface User {
    id: string;
    username: string;
    userData: UserData;
    isAdmin: number;
    allocations: UserAllocation[];
}

export interface UserTable {
    id: string;
    username: string;
    name: string;
    surname: string;
    email: string;
    isAdmin: number;
    job: string;
}

export interface UserTableHeader {
    id: keyof UserTable;
    numeric: boolean;
    label: string;
}

export interface UserFormData {
    username: string;
    password: string;
    passwordRepeat: string;
    isAdmin: boolean;
    name: string;
    surname: string;
    email: string;
    phone: string;
    addressName: string;
    postalCode: string;
    city: string;
    number: string;
    province: string;
    floor: string;
    door: string;
    other: string;
    allocations: { idSupermarket: string; idJob: string }[];
}
