import { useNavigate } from "react-router";
import { useSession } from "../../../login/contexts/SessionContext.ts";
import { type ChangeEvent, type FormEvent, useEffect, useRef, useState } from "react";
import { ProductService } from "../../services/ProductService.ts";
import Paper from "@mui/material/Paper";
import {
    FormControl, FormControlLabel, FormHelperText,
    Grid, IconButton,
    InputLabel,
    MenuItem,
    Select,
    type SelectChangeEvent, Switch,
    TextField,
    Typography
} from "@mui/material";
import Box from "@mui/material/Box";
import { ButtonComponent } from "../../../commons/components/Buttons/ButtonComponent.tsx";
import { Delete } from '@mui/icons-material';
import type { Category, ProductFormData, Supplier, Tax } from "../../types/ProductTypes.ts";
import { CategoryService } from "../../services/CategoryService.ts";
import { TaxService } from "../../services/TaxService.ts";
import { SupplierService } from "../../services/SupplierService.ts";

const initialFormData: ProductFormData = {
    name: "",
    ean: "",
    image: "",
    category: {id: "", name: ""},
    price: "",
    tax: {id: "", name: "", percent: ""},
    supplier: {id: "", name: "", phone: "", email: "", contact: ""},
    stock: "",
    active: false,
}

export const ProductCreatePage = () => {
    const navigate = useNavigate();
    const {session} = useSession();
    const [formData, setFormData] = useState<ProductFormData>(initialFormData);
    const [errors, setErrors] = useState<Partial<Record<keyof ProductFormData, string>>>({});
    const [selectedFile, setSelectedFile] = useState<File | null>(null);
    const [previewUrl, setPreviewUrl] = useState<string>('');
    const [isUploading, setIsUploading] = useState(false);
    const [categories, setCategories] = useState<Category[]>([]);
    const [taxes, setTaxes] = useState<Tax[]>([]);
    const [suppliers, setSuppliers] = useState<Supplier[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const fileInputRef = useRef<HTMLInputElement>(null);

    const fetchCategories = async () => {
        try {
            const fetchedCategories = await CategoryService.getCategories(session);
            setCategories(fetchedCategories);
        } catch (error) {
        }
    };

    const fetchTaxes = async () => {
        try {
            const fetchedTaxes = await TaxService.getTaxes(session);
            setTaxes(fetchedTaxes);
        } catch (error) {
        }
    };

    const fetchSuppliers = async () => {
        try {
            const fetchedSuppliers = await SupplierService.getSuppliers(session);
            setSuppliers(fetchedSuppliers);
        } catch (error) {
        }
    };

    const fetchFieldsData = async () => {
        setIsLoading(true);
        await Promise.all([
            fetchCategories(),
            fetchTaxes(),
            fetchSuppliers()
        ]);
        setIsLoading(false);
    }

    useEffect(() => {
        fetchFieldsData();
    }, [session])

    const handleInputChange = (field: keyof ProductFormData) => (
        event: ChangeEvent<HTMLInputElement>
    ) => {
        const value = field === 'active'
            ? event.target.checked
            : event.target.value;

        setFormData(prevFormData => ({
            ...prevFormData,
            [field]: value
        }));

        if (errors[field]) {
            setErrors(prevErrors => ({
                ...prevErrors,
                [field]: undefined
            }));
        }
    };

    const handleSelectChange = (field: keyof ProductFormData) => (
        event: SelectChangeEvent
    ) => {
        if (field === 'category') {
            const selectedCategory = categories.find(category => category.id === event.target.value);
            if (selectedCategory) {
                setFormData(prevFormData => ({
                    ...prevFormData,
                    category: selectedCategory,
                }));
            }
        } else if (field === 'tax') {
            const selectedTax = taxes.find(tax => tax.id === event.target.value);
            if (selectedTax) {
                setFormData(prevFormData => ({
                    ...prevFormData,
                    tax: selectedTax,
                }));
            }
        } else if(field === 'supplier') {
            const selectedSupplier = suppliers.find(supplier => supplier.id === event.target.value);
            if (selectedSupplier) {
                setFormData(prevFormData => ({
                    ...prevFormData,
                    supplier: selectedSupplier,
                }));
            }
        } else {
            setFormData(prevFormData => ({
                ...prevFormData,
                [field]: event.target.value
            }));
        }

        if (errors[field]) {
            setErrors(prevErrors => ({
                ...prevErrors,
                [field]: undefined
            }));
        }
    }

    const handleFileChange = (event: ChangeEvent<HTMLInputElement>) => {
        const file = event.target.files?.[0];
        if (file) {
            if (!file.type.startsWith('image/')) {
                setErrors(prevErrors => ({
                    ...prevErrors,
                    image: 'El archivo debe ser una imagen'
                }));
                return;
            }

            setSelectedFile(file);
            setErrors(prevErrors => ({
                ...prevErrors,
                image: undefined
            }));

            const reader = new FileReader();
            reader.onloadend = () => {
                setPreviewUrl(reader.result as string);
            };
            reader.readAsDataURL(file);
        }
    }

    const handleRemoveImage = () => {
        setSelectedFile(null);
        setPreviewUrl('');
        setFormData(prevFormData => ({
            ...prevFormData,
            image: ''
        }));

        if (fileInputRef.current) {
            fileInputRef.current.value = '';
        }
    }

    const validateForm = (): boolean => {
        const newErrors: Partial<Record<keyof ProductFormData, string>> = {};

        if (!formData.name.trim()) {
            newErrors.name = 'El nombre del producto es obligatorio';
        }
        if (!formData.ean.trim()) {
            newErrors.ean = 'El EAN del producto es obligatorio';
        }
        if (!formData.category.id) {
            newErrors.category = "La categoría del producto es obligatoria";
        }
        if (!formData.price || isNaN(Number(formData.price))) {
            newErrors.price = 'El precio debe ser un número válido';
        }
        if (!formData.stock || isNaN(Number(formData.stock))) {
            newErrors.stock = 'El stock debe ser un número válido';
        }
        if (!formData.tax.id) {
            newErrors.tax = 'El impuesto del producto es obligatorio';
        }
        if (!formData.supplier.id) {
            newErrors.supplier = 'El proveedor del producto es obligatorio';
        }

        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    };

    const handleSubmit = async (event: FormEvent) => {
        event.preventDefault();

        if (!validateForm()) return;

        try {
            setIsUploading(true);

            await ProductService.createProduct(
                {
                    ...formData,
                    image: '',
                    price: Number(formData.price),
                    stock: Number(formData.stock),
                    active: formData.active ? 1 : 0,
                },
                selectedFile,
                session
            );

            setIsUploading(false);
            navigate('/productos/catalogo');

        } catch (error) {
            setIsUploading(false);
        }
    };

    return (
        <Paper sx={{p: 3, maxWidth: 800, margin: 'auto', mt: 4}}>
            <Typography variant="h5" gutterBottom>
                Añadir nuevo producto
            </Typography>
            <Box component="form" onSubmit={handleSubmit} noValidate>
                <Grid container spacing={3}>
                    <Grid size={{xs: 12, sm: 6}}>
                        <TextField
                            fullWidth
                            label="Nombre"
                            name="name"
                            value={formData.name}
                            onChange={handleInputChange('name')}
                            error={!!errors.name}
                            helperText={errors.name}
                            required
                        />
                    </Grid>
                    <Grid size={{xs: 12, sm: 6}}>
                        <TextField
                            fullWidth
                            label="EAN"
                            name="ean"
                            value={formData.ean}
                            onChange={handleInputChange('ean')}
                            error={!!errors.ean}
                            helperText={errors.ean}
                            required
                        />
                    </Grid>
                    <Grid size={{xs: 12}}>
                        <input
                            type="file"
                            accept="image/*"
                            style={{display: 'none'}}
                            ref={fileInputRef}
                            onChange={handleFileChange}
                        />
                        <Box sx={{display: 'flex', alignItems: 'center', gap: 2}}>
                            <ButtonComponent
                                text="Seleccionar imagen"
                                type="button"
                                variant="outlined"
                                color="primary"
                                size="small"
                                onClick={() => fileInputRef.current?.click()}
                                disabled={isUploading}
                            />
                            {selectedFile && (
                                <IconButton
                                    onClick={handleRemoveImage}
                                    disabled={isUploading}
                                >
                                    <Delete/>
                                </IconButton>
                            )}
                        </Box>
                        {errors.image && (
                            <Typography color="error" variant="caption" display="block">
                                {errors.image}
                            </Typography>
                        )}
                        {previewUrl && (
                            <Box sx={{mt: 2, maxWidth: 200}}>
                                <img
                                    src={previewUrl}
                                    alt="Preview"
                                    style={{
                                        width: '100%',
                                        height: 'auto',
                                        borderRadius: '4px'
                                    }}
                                />
                            </Box>
                        )}
                    </Grid>
                    <Grid size={{xs: 12, sm: 6}}>
                        <FormControl
                            fullWidth
                            required
                            error={!!errors.category}
                            disabled={isLoading}
                        >
                            <InputLabel>Categoría</InputLabel>
                            <Select
                                value={formData.category.id}
                                label="Categoría"
                                onChange={handleSelectChange('category')}
                            >
                                {categories.map((category) => (
                                    <MenuItem key={category.id} value={category.id}>
                                        {category.name}
                                    </MenuItem>
                                ))}
                            </Select>
                            {errors.category && (
                                <FormHelperText>{errors.category}</FormHelperText>
                            )}
                        </FormControl>
                    </Grid>
                    <Grid size={{xs: 12, sm: 6}}>
                        <TextField
                            fullWidth
                            label="Precio"
                            name="price"
                            type="number"
                            value={formData.price}
                            onChange={handleInputChange('price')}
                            error={!!errors.price}
                            helperText={errors.price}
                            required
                            slotProps={{htmlInput: {step: '0.01'}}}
                        />
                    </Grid>
                    <Grid size={{xs: 12, sm: 6}}>
                        <FormControl
                            fullWidth
                            required
                            error={!!errors.tax}
                            disabled={isLoading}
                        >
                            <InputLabel>IVA</InputLabel>
                            <Select
                                value={formData.tax.id}
                                label="IVA"
                                onChange={handleSelectChange('tax')}
                            >
                                {taxes.map((tax) => (
                                    <MenuItem key={tax.id} value={tax.id}>
                                        {tax.name} ({tax.percent}%)
                                    </MenuItem>
                                ))}
                            </Select>
                            {errors.tax && (
                                <FormHelperText>{errors.tax}</FormHelperText>
                            )}
                        </FormControl>
                    </Grid>
                    <Grid size={{xs: 12, sm: 6}}>
                        <TextField
                            fullWidth
                            label="Stock"
                            name="stock"
                            type="number"
                            value={formData.stock}
                            onChange={handleInputChange('stock')}
                            error={!!errors.price}
                            helperText={errors.price}
                            required
                        />
                    </Grid>
                    <Grid size={{xs: 12, sm: 6}}>
                        <FormControl
                            fullWidth
                            required
                            error={!!errors.supplier}
                            disabled={isLoading}
                        >
                            <InputLabel>Proveedor</InputLabel>
                            <Select
                                value={formData.supplier.id}
                                label="Proveedor"
                                onChange={handleSelectChange('supplier')}
                            >
                                {suppliers.map((supplier) => (
                                    <MenuItem key={supplier.id} value={supplier.id}>
                                        {supplier.name}
                                    </MenuItem>
                                ))}
                            </Select>
                            {errors.supplier && (
                                <FormHelperText>{errors.supplier}</FormHelperText>
                            )}
                        </FormControl>
                    </Grid>
                    <Grid size={{xs: 12}}>
                        <FormControlLabel
                            control={
                                <Switch
                                    checked={formData.active}
                                    onChange={handleInputChange('active')}
                                    color="primary"
                                />
                            }
                            label="Activo"
                        />
                    </Grid>
                    <Grid size={{xs: 12}} sx={{display: 'flex', justifyContent: 'flex-end', gap: 2}}>
                        <ButtonComponent
                            text="Guardar"
                            type="submit"
                            variant="contained"
                            color="primary"
                            size="medium"
                            onClick={() => {
                            }}
                            disabled={isUploading}
                        />
                    </Grid>
                </Grid>
            </Box>
        </Paper>
    );


};