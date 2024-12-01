import React from 'react';
import { Table } from 'antd';

const columns = [
    {
        title: 'Num',
        dataIndex: 'numOrder',
    },
    {
        title: 'Estado',
        dataIndex: 'status',
    },
    {
        title: 'Cliente',
        dataIndex: 'customer',
    },
    {
        title: 'Fecha',
        dataIndex: 'date',
    },
    {
        title: 'Importe',
        dataIndex: 'amount',
    },
];

const dataOrders = [];
for (let i = 0; i < 46; i++) {
    dataOrders.push({
        numOrder: i,
        status: "Completado",
        customer: `Fran García ${i}`,
        date: "05-05-2023",
        amount: "250€"
    });
}

const BrandList = () => {
    return (
        <div>
            <h3 className="mb-4">Listado de marcas</h3>
            <div>
                <Table columns={columns} dataSource={dataOrders} />
            </div>
        </div>
    );
};

export default BrandList;