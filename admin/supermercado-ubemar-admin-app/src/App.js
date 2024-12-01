import './App.css';
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import Dashboard from './pages/Dashboard';
import Login from './pages/Login';
import ResetPassword from './pages/ResetPassword';
import ForgotPassword from './pages/ForgotPassword';
import MainLayout from './components/MainLayout';
import RecentOrders from './pages/RecentOrders';
import Orders from './pages/Orders';
import Customers from './pages/Customers';
import ProductList from './pages/ProductList';
import CategoryList from './pages/CategoryList';
import BrandList from './pages/BrandList';

function App() {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<Login />} />
        <Route path="/restablecer-contraseña" element={<ResetPassword />} />
        <Route path="/olvido-contraseña" element={<ForgotPassword />} />
        <Route path="/admin" element={<MainLayout />} >
          <Route index element={<Dashboard />} />
          <Route path="pedidos-general" element={<Orders />} />
          <Route path="pedidos-recientes" element={<RecentOrders />} />
          <Route path="clientes" element={<Customers />} />
          <Route path="listado-productos" element={<ProductList />} />
          <Route path="listado-categorias" element={<CategoryList />} />
          <Route path="listado-marcas" element={<BrandList />} />
        </Route>
      </Routes>
    </Router>
  );
};

export default App;
