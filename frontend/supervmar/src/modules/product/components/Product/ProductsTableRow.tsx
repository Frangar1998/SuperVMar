import type { TableData } from "../../../commons/components/Table/TableData.ts";
import type { ProductTable } from "../../types/ProductTypes.ts";
import TableCell from "@mui/material/TableCell";
import { ImageComponent } from "../../../commons/components/ImageComponent.tsx";

export const ProductsTableRow = (row: TableData<ProductTable>) => (
    <>
        <TableCell align="left">
            {row.data.image ? (
                <ImageComponent
                    path={row.data.image}
                    altImage="Product"
                    style={{width: '50px', height: '50px'}}
                />
            ) : ("Sin imagen")
            }
        </TableCell>
        <TableCell align="left">{row.data.name}</TableCell>
        <TableCell align="left">{row.data.ean}</TableCell>
        <TableCell align="left">{row.data.category}</TableCell>
        <TableCell align="left">{row.data.price}€</TableCell>
        <TableCell align="left">{row.data.tax}</TableCell>
        <TableCell align="left">{row.data.stock}</TableCell>
        <TableCell align="left">{row.data.active ? "Sí" : 'No'}</TableCell>
    </>
);