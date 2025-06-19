interface ImageProps {
    path: string;
    altImage: string;
    style?: any;
}

export const ImageComponent = (imageProps: ImageProps) => {
    const baseUrl = import.meta.env.VITE_PUBLIC_URL;
    const imagePath = `${baseUrl}${imageProps.path}`;

    return (
        <img
            src={imagePath}
            alt={imageProps.altImage}
            style={imageProps.style}
        />
    );
};