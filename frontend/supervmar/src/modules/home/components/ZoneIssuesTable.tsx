import Box from "@mui/material/Box";
import Paper from "@mui/material/Paper";
import Typography from "@mui/material/Typography";
import Table from "@mui/material/Table";
import TableBody from "@mui/material/TableBody";
import TableCell from "@mui/material/TableCell";
import TableContainer from "@mui/material/TableContainer";
import TableHead from "@mui/material/TableHead";
import TableRow from "@mui/material/TableRow";
import type { ZoneRestockInfo } from "../types/DashboardTypes.ts";

const STATUS_COLORS: Record<string, string> = {
    ok: "#4CAF50",
    warning: "#FF9800",
    critical: "#F44336",
};

interface ZoneIssuesTableProps {
    zones: ZoneRestockInfo[];
}

export const ZoneIssuesTable = ({ zones }: ZoneIssuesTableProps) => {
    const zonesWithIssues = zones.filter((z) => z.status !== "ok");

    if (zonesWithIssues.length === 0) return null;

    return (
        <Paper sx={{ p: 2 }} elevation={2}>
            <Typography variant="h6" gutterBottom>
                Zonas con incidencias
            </Typography>
            <TableContainer>
                <Table size="small">
                    <TableHead>
                        <TableRow>
                            <TableCell>Zona</TableCell>
                            <TableCell align="right">Espacios vacíos</TableCell>
                            <TableCell align="right">Stock bajo</TableCell>
                            <TableCell>Estado</TableCell>
                        </TableRow>
                    </TableHead>
                    <TableBody>
                        {zonesWithIssues.map((zone) => (
                            <TableRow key={zone.zoneId}>
                                <TableCell>{zone.zoneName}</TableCell>
                                <TableCell align="right">{zone.emptySpaces}</TableCell>
                                <TableCell align="right">{zone.lowStockSpaces}</TableCell>
                                <TableCell>
                                    <Box
                                        sx={{
                                            display: "inline-block",
                                            width: 12,
                                            height: 12,
                                            borderRadius: "50%",
                                            bgcolor: STATUS_COLORS[zone.status],
                                            mr: 1,
                                            verticalAlign: "middle",
                                        }}
                                    />
                                    {zone.status === "critical" ? "Crítico" : "Atención"}
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </TableContainer>
        </Paper>
    );
};
