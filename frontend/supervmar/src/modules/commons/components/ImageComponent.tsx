interface ImageProps {
    image: string;
    altImage: string;
    className: string;
}

export const ImageComponent = (imageProps: ImageProps) => {
    return (
        <img
            src={imageProps.image}
            alt={imageProps.altImage}
            className={imageProps.className}
        />
    );
};