import React from 'react';
import CustomInput from '../components/CustomInput';

const ResetPassword = () => {
    return (
        <div className="py-5" style={{ background: "#ffd333", minHeight: "100vh" }}>
            <br />
            <br />
            <br />
            <br />
            <br />
            <div className="my-5 w-25 bg-white rounded-3 mx-auto p-4">
                <h3 className="text-center">Restablece tu contraseña</h3>
                <p className="text-center">
                    Por favor, introduzca su nueva contraseña
                </p>
                <form action="">
                    <CustomInput type="password" label="Nueva contraseña" id="pass" />
                    <CustomInput type="password" label="Repite nueva contraseña" id="repeatpass" />
                    <button type="submit" className="border-0 px-3 py-2 text-white fw-bold w-100" style={{ background: "#ffd333" }}>
                        Restablecer
                    </button>
                </form>
            </div>
        </div>
    );
};

export default ResetPassword;