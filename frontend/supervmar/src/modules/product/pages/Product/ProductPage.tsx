import { useNavigate, useParams } from "react-router";
import Box from "@mui/material/Box";
import { Button, Card, CardActions, CardContent, Typography } from "@mui/material";
import { Delete, Save } from '@mui/icons-material';
import { useEffect, useState } from "react";
import type { Product } from "../../types/ProductTypes.ts";

interface ProductDetails extends Product {

}

export const ProductPage = () => {
    const {id} = useParams();
    const [product, setProduct] = useState<ProductDetails | null>(null);
    const [loading, setLoading] = useState(true);
    const navigate = useNavigate();

    useEffect(() => {
        const fetchProduct = async () => {
            try {
                setLoading(true);
                const response = await fetch(`/api/v1/product?field=id&value=${id}`);
                const data = await response.json();
                setProduct(data);
            } catch (error) {
                console.log(error);
            } finally {
                setLoading(false);
            }
        }
    }, []);

    const handleEdit = async () => {

    }

    const handleDelete = async () => {
        navigate(`/productos/catalogo`);
    }

    return (
        <Box sx={{p: 3}}>
            <Card>
                <CardContent>
                    <Typography variant="h5" component="h1" gutterBottom>
                        Detalles del producto
                    </Typography>
                </CardContent>
                <CardActions>
                    <Button
                        startIcon={<Save/>}
                        variant="contained"
                        onClick={handleEdit}
                    >
                        Guardar
                    </Button>
                    <Button
                        startIcon={<Delete/>}
                        variant="contained"
                        color="error"
                        onClick={handleDelete}
                    >
                        Eliminar
                    </Button>
                </CardActions>
            </Card>
        </Box>
    );
};