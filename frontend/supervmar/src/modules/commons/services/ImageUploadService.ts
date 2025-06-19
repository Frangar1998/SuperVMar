import type { CustomSession } from "../../login/contexts/SessionContext.ts";
import { HttpService } from "./HttpService.ts";

export const ImageUploadService =  {
    uploadImage: async (file: File, type: string, session: CustomSession): Promise<string> => {
        try {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('type', type);

            const response = await HttpService.api({
                endpoint: '/upload/image',
                method: 'POST',
                body: formData
            }, session);

            return response.path;
        } catch (error) {
            throw new Error('Error al guardar la imagen.');
        }
    }
};