import { useEffect, useState, useCallback } from "react";
import Box from "@mui/material/Box";
import { useSession } from "../../../login/contexts/SessionContext.ts";
import { LoadingComponent } from "../../../commons/components/LoadingComponent.tsx";
import { ErrorSnackbarComponent } from "../../../commons/components/ErrorSnackbarComponent.tsx";
import { SupermarketService } from "../../../supermarket/services/SupermarketService.ts";
import { ProductService } from "../../services/ProductService.ts";
import { CategoryService } from "../../services/CategoryService.ts";
import { AllocationService } from "../../services/AllocationService.ts";
import type { ProductAllocation } from "../../services/AllocationService.ts";
import type { ZoneFormData } from "../../../supermarket/types/SupermarketTypes.ts";
import type { Product, Category } from "../../types/ProductTypes.ts";
import { AllocationMapComponent } from "../../components/Allocation/AllocationMapComponent.tsx";

export const AllocationsPage = () => {
    const { session } = useSession();
    const [zones, setZones] = useState<ZoneFormData[]>([]);
    const [products, setProducts] = useState<Product[]>([]);
    const [categories, setCategories] = useState<Category[]>([]);
    const [allocations, setAllocations] = useState<Map<string, ProductAllocation>>(new Map());
    const [loading, setLoading] = useState(true);
    const [snackbar, setSnackbar] = useState<{ open: boolean; message: string; severity: "success" | "error" }>({
        open: false, message: '', severity: 'success'
    });

    const fetchData = async () => {
        try {
            setLoading(true);
            const [supermarkets, prods, cats, allocs] = await Promise.all([
                SupermarketService.getSupermarkets(session),
                ProductService.getProductsFull(session),
                CategoryService.getCategories(session),
                AllocationService.getAllocations(session),
            ]);

            if (supermarkets.length > 0) {
                const s = supermarkets[0];
                setZones(s.zones?.map(z => ({
                    id: z.id,
                    name: z.name,
                    cornerTopLeft: z.cornerTopLeft,
                    cornerTopRight: z.cornerTopRight,
                    cornerBottomRight: z.cornerBottomRight,
                    cornerBottomLeft: z.cornerBottomLeft,
                    spaces: z.spaces?.map(sp => ({
                        id: sp.id,
                        position: sp.position,
                        maxSpots: sp.maxSpots,
                    })) ?? [],
                })) ?? []);
            }

            setProducts(prods);
            setCategories(cats);

            const allocMap = new Map<string, ProductAllocation>();
            for (const a of allocs) {
                allocMap.set(a.space.id, a);
            }
            setAllocations(allocMap);
        } catch (error) {
            console.error('Error fetching allocation data:', error);
        } finally {
            setLoading(false);
        }
    };

    const refreshAllocations = async () => {
        try {
            const allocs = await AllocationService.getAllocations(session);
            const allocMap = new Map<string, ProductAllocation>();
            for (const a of allocs) {
                allocMap.set(a.space.id, a);
            }
            setAllocations(allocMap);
        } catch (error) {
            console.error('Error refreshing allocations:', error);
        }
    };

    useEffect(() => {
        fetchData();
    }, []);

    const handleAssign = useCallback(async (spaceId: string, productId: string, quantity: number) => {
        await AllocationService.assignProduct(spaceId, productId, quantity, session);
        await refreshAllocations();
        setSnackbar({ open: true, message: 'Producto asignado correctamente', severity: 'success' });
    }, [session]);

    const handleRemove = useCallback(async (spaceId: string) => {
        await AllocationService.removeAllocation(spaceId, session);
        await refreshAllocations();
        setSnackbar({ open: true, message: 'Asignación eliminada correctamente', severity: 'success' });
    }, [session]);

    if (loading) {
        return <LoadingComponent />;
    }

    return (
        <Box>
            <AllocationMapComponent
                zones={zones}
                allocations={allocations}
                products={products}
                categories={categories}
                onAssign={handleAssign}
                onRemove={handleRemove}
            />

            <ErrorSnackbarComponent
                open={snackbar.open}
                message={snackbar.message}
                severity={snackbar.severity}
                onClose={() => setSnackbar(prev => ({ ...prev, open: false }))}
            />
        </Box>
    );
};