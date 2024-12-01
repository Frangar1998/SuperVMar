import { MenuFoldOutlined, MenuUnfoldOutlined } from '@ant-design/icons';
import { Button, Layout, Menu, theme } from 'antd';
import { useState } from 'react';
import { RiDashboardFill } from 'react-icons/ri';
import { GiFruitBowl, GiShoppingCart } from 'react-icons/gi';
import { FiUsers } from 'react-icons/fi';
import { MdOutlineCategory } from 'react-icons/md';
import { ImPriceTags } from 'react-icons/im';
import { IoMdNotificationsOutline, IoIosStats } from 'react-icons/io';
import { useNavigate } from 'react-router-dom';
import { Outlet } from 'react-router-dom';

const { Header, Sider, Content } = Layout;

const MainLayout = () => {
    const [collapsed, setCollapsed] = useState(false);
    const {
        token: { colorBgContainer },
    } = theme.useToken();
    const navigate = useNavigate();
    return (
        <Layout>
            <Sider trigger={null} collapsible collapsed={collapsed}>
                <div className="logo">
                    <h2 className="text-white fs-5 text-center py-3 mb-0">
                        <span className="sm-logo">UA</span>
                        <span className="lg-logo">
                            Ubemar Admin
                        </span>
                    </h2>
                </div>
                <Menu
                    theme="dark"
                    mode="inline"
                    defaultSelectedKeys={['']}
                    onClick={({ key }) => {
                        if (key === 'cerrar-sesion') {

                        } else {
                            navigate(key);
                        }
                    }}
                    items={[
                        {
                            key: '',
                            icon: <RiDashboardFill className="fs-3" />,
                            label: 'Portada',
                        },
                        {
                            key: 'clientes',
                            icon: <FiUsers className="fs-3" />,
                            label: 'Clientes',
                        },
                        {
                            key: 'productos',
                            icon: <GiFruitBowl className="fs-3" />,
                            label: 'Productos',
                            children: [
                                {
                                    key: 'añadir-productos',
                                    icon: <GiFruitBowl className="fs-4" />,
                                    label: 'Añadir productos',
                                },
                                {
                                    key: 'listado-productos',
                                    icon: <GiFruitBowl className="fs-4" />,
                                    label: 'Listado de productos',
                                },
                                {
                                    key: 'añadir-categorias',
                                    icon: <MdOutlineCategory className="fs-4" />,
                                    label: 'Añadir categorías',
                                },
                                {
                                    key: 'listado-categorias',
                                    icon: <MdOutlineCategory className="fs-4" />,
                                    label: 'Listado de categorías',
                                },
                                {
                                    key: 'añadir-marcas',
                                    icon: <ImPriceTags className="fs-4" />,
                                    label: 'Añadir marcas',
                                },
                                {
                                    key: 'listado-marcas',
                                    icon: <ImPriceTags className="fs-4" />,
                                    label: 'Listado de marcas',
                                },
                            ]
                        },
                        {
                            key: 'pedidos',
                            icon: <GiShoppingCart className="fs-3" />,
                            label: 'Pedidos',
                            children: [
                                {
                                    key: 'pedidos-general',
                                    icon: <GiShoppingCart className="fs-4" />,
                                    label: 'Pedidos general',
                                },
                                {
                                    key: 'pedidos-recientes',
                                    icon: <GiShoppingCart className="fs-4" />,
                                    label: 'Pedidos recientes',
                                },
                            ]
                        },
                        {
                            key: 'estadisticas',
                            icon: <IoIosStats className="fs-3" />,
                            label: 'Estadísticas',
                        },
                    ]}
                />
            </Sider>
            <Layout>
                <Header
                    className="d-flex justify-content-between ps-1 pe-5"
                    style={{
                        padding: 0,
                        background: colorBgContainer,
                    }}
                >
                    <Button
                        type="text"
                        icon={collapsed ? <MenuUnfoldOutlined /> : <MenuFoldOutlined />}
                        onClick={() => setCollapsed(!collapsed)}
                        style={{
                            fontSize: '16px',
                            width: 64,
                            height: 64,
                        }}
                    />
                    <div className="d-flex gap-4 align-items-center">
                        <div className="position-relative">
                            <IoMdNotificationsOutline className="fs-4" />
                            <span className="badge bg-warning rounded-rounded-circle p-1 position-absolute">3</span>
                        </div>
                        <div className="d-flex gap-3 align-items-cender">
                            <div>
                                <h5 className="mb-0">Fran</h5>
                                <p className="mb-0">franky23398@gmail.com</p>
                            </div>
                        </div>
                    </div>
                </Header>
                <Content
                    style={{
                        margin: '24px 16px',
                        padding: 24,
                        minHeight: 280,
                        background: colorBgContainer,
                    }}
                >
                    <Outlet />
                </Content>
            </Layout>
        </Layout>
    );
};
export default MainLayout;