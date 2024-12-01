import React from 'react';
import CustomInput from '../components/CustomInput';
import { Link } from 'react-router-dom';

const Login = () => {
    return (
        <div className="py-5" style={{ background: "#ffd333", minHeight: "100vh" }}>
            <br />
            <br />
            <br />
            <br />
            <br />
            <div className="my-5 w-25 bg-white rounded-3 mx-auto p-4">
                <h3 className="text-center">Iniciar sesión</h3>
                <form action="">
                    <CustomInput type="text" label="Email" id="email" />
                    <CustomInput type="password" label="Contraseña" id="pass" />
                    <div className="mb-3 text-end">
                        <Link to="/olvido-contraseña">¿Olvidó su contraseña?</Link>
                    </div>
                    <Link to="/admin" type="submit" className="border-0 px-3 py-2 text-white fw-bold w-100 text-center text-decoration-none fs-5" style={{ background: "#ffd333" }}>
                        Iniciar sesión
                    </Link>
                </form>
            </div>
        </div>
    );
};

export default Login;