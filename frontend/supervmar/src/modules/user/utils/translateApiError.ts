const ERROR_TRANSLATIONS: Record<string, string> = {
    'The password does not meet the following requirements:': 'La contraseña no cumple los siguientes requisitos:',
    'The password must have at least 12 characters and at max 100 characters.': 'La contraseña debe tener entre 12 y 100 caracteres.',
    'The password must has at least one number.': 'La contraseña debe tener al menos un número.',
    'The password must has at least one uppercase letter.': 'La contraseña debe tener al menos una letra mayúscula.',
    'The password must has at least one lowercase letter.': 'La contraseña debe tener al menos una letra minúscula.',
    'The password must has at least one of these special characters: #?!@$%^&*_': 'La contraseña debe tener al menos uno de estos caracteres especiales: #?!@$%^&*_',
    'The passwords must be the same.': 'Las contraseñas deben ser iguales.',
    'The current password is not correct.': 'La contraseña actual no es correcta.',
    'User admin cannot be deleted.': 'No se puede eliminar un usuario administrador.',
};

export const translateApiError = (message: string): string => {
    let translated = message;
    for (const [en, es] of Object.entries(ERROR_TRANSLATIONS)) {
        translated = translated.split(en).join(es);
    }
    return translated;
};
