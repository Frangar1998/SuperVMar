import React from 'react';
import { FiTrendingUp, FiTrendingDown } from 'react-icons/fi';
import { Column } from '@ant-design/plots';
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

const Dashboard = () => {
    const data = [
        {
            type: 'Enero',
            sales: 38,
        },
        {
            type: 'Febrero',
            sales: 52,
        },
        {
            type: 'Marzo',
            sales: 61,
        },
        {
            type: 'Abril',
            sales: 145,
        },
        {
            type: 'Mayo',
            sales: 48,
        },
        {
            type: 'Junio',
            sales: 38,
        },
        {
            type: 'Julio',
            sales: 38,
        },
        {
            type: 'Agosto',
            sales: 38,
        },
        {
            type: 'Septiembre',
            sales: 38,
        },
        {
            type: 'Octubre',
            sales: 38,
        },
        {
            type: 'Noviembre',
            sales: 38,
        },
        {
            type: 'Diciembre',
            sales: 38,
        },
    ];
    const config = {
        data,
        xField: 'type',
        yField: 'sales',
        color: ({ type }) => {
            return "#ffd333";
        },
        label: {
            position: 'middle',
            style: {
                fill: '#FFFFFF',
                opacity: 1,
            },
        },
        xAxis: {
            label: {
                autoHide: true,
                autoRotate: false,
            },
        },
        meta: {
            type: {
                alias: 'Mes',
            },
            sales: {
                alias: 'Venta',
            },
        },
    };
    return (
        <div>
            <h3 className="mb-4">Portada</h3>
            <div className="d-flex justify-content-between align-items-center gap-3">
                <div className="d-flex justify-content-between align-items-end flex-grow-1 bg-white p-3 rounded-3">
                    <div>
                        <p>Ingresos</p>
                        <h4 className="mb-0">5000€</h4>
                    </div>
                    <div className="d-flex flex-column align-items-end">
                        <h6 className="profit"><FiTrendingUp /> 40%</h6>
                        <p className="mb-0">Respecto a Abril 2023</p>
                    </div>
                </div>
                <div className="d-flex justify-content-between align-items-end flex-grow-1 bg-white p-3 rounded-3">
                    <div>
                        <p>Ingresos</p>
                        <h4 className="mb-0">5000€</h4>
                    </div>
                    <div className="d-flex flex-column align-items-end">
                        <h6 className="profit"><FiTrendingUp /> 40%</h6>
                        <p className="mb-0">Respecto a Abril 2023</p>
                    </div>
                </div>
                <div className="d-flex justify-content-between align-items-end flex-grow-1 bg-white p-3 rounded-3">
                    <div>
                        <p>Ingresos</p>
                        <h4 className="mb-0">5000€</h4>
                    </div>
                    <div className="d-flex flex-column align-items-end">
                        <h6 className="loss"><FiTrendingDown /> 40%</h6>
                        <p className="mb-0">Respecto a Abril 2023</p>
                    </div>
                </div>
            </div>
            <div className="mt-4">
                <h3 className="mb-4">
                    Estadísticas de ingresos
                </h3>
                <div>
                    <Column {...config} />
                </div>
            </div>
            <div className="mt-4">
                <h3 className="mb-4">
                    Pedidos recientes
                </h3>
                <div>
                    <Table columns={columns} dataSource={dataOrders} />
                </div>
            </div>
        </div>
    );
};

export default Dashboard;