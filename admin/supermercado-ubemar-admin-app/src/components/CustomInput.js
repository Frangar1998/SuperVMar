import React from 'react'

const CustomInput = (props) => {
    const {type, label, input_id, input_class } = props;
    return (
        <div class="form-floating mb-3">
            <input type={type} class={`form-control ${input_class}`} id={input_id} placeholder={label} />
                <label htmlFor={label}>{label}</label>
        </div>
    );
};

export default CustomInput;