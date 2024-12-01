import React from 'react';
import { CustomInput } from '../components/CustomInput';

const AddCategory = () => {
    return (
        <div>
            <h3 className="mb-4">Añadir categorías</h3>
            <div className="">
                <form action="">
                    <CustomInput />
                </form>
            </div>
        </div>
    );
};

export default AddCategory;