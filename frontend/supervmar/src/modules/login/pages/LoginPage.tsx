import { useSession } from "../contexts/SessionContext.ts";
import { useNavigate } from "react-router";
import { SignInPage } from "@toolpad/core";
import '../styles/login.css';
import { LoginButtonComponent } from "../components/LoginButtonComponent.tsx";
import { login } from "../services/LoginService.ts";

export const LoginPage = () => {
    const { session, setSession} = useSession();
    const navigate = useNavigate();


    return (
        <>
            <SignInPage
                providers={[{id: 'credentials', name: ""}]}
                localeText={{
                    signInSubtitle: "",
                    signInTitle: "",
                }}
                slots={{
                    submitButton: LoginButtonComponent
                }}
                slotProps={{
                    emailField: {
                        id: 'user',
                        title: 'Usuario',
                        name: 'user',
                        label: 'Usuario',
                        placeholder: 'Nombre de usuario',
                        type: "text",
                        autoComplete: "user"
                    },
                    passwordField: {
                        placeholder: 'Contraseña',
                        label: 'Contraseña'
                    },
                }}
                signIn={async (_provider, formData: any, callbackUrl) => {
                    try {
                        const response = await login({username: formData.get('user'), password: formData.get('password')}, session );
                        if (response) {
                            setSession(response);
                            localStorage.setItem('session', JSON.stringify(response));
                            navigate(callbackUrl || '/', { replace: true });
                            return {};
                        }
                    } catch (error) {
                        return { error: 'Usuario o contraseña incorrectos' };
                    }
                    return {};
                }}
            />
        </>
    );
};