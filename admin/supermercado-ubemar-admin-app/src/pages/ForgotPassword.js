import React from 'react';
import CustomInput from '../components/CustomInput';

const ForgotPassword = () => {
    return (
        <div className="py-5" style={{ background: "#ffd333", minHeight: "100vh" }}>
            <br />
            <br />
            <br />
            <br />
            <br />
            <div className="my-5 w-25 bg-white rounded-3 mx-auto p-4">
                <h3 className="text-center">Olvidó su contraseña</h3>
                <p className="text-center">
                    Por favor, introduzca su email para restablecer su contraseña
                </p>
                <form action="">
                    <CustomInput type="text" label="Email" id="email" />
                    <button type="submit" className="border-0 px-3 py-2 text-white fw-bold w-100" style={{ background: "#ffd333" }}>
                        Enviar
                    </button>
                </form>
            </div>
        </div>
    );
};

export default ForgotPassword;