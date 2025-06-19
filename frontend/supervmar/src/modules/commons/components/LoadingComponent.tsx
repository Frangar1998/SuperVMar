import Box from "@mui/material/Box";
import { Skeleton } from "@mui/material";

export const LoadingComponent = () => {
    return (
        <Box sx={{ p: 3 }}>
            <Skeleton variant="rectangular" height={200} />
            <Skeleton variant="text" sx={{ mt: 2 }} />
            <Skeleton variant="text" />
            <Skeleton variant="text" width="60%" />
        </Box>
    );
};