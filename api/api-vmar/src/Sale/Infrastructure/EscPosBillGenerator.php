<?php

namespace SuperVMar\Sale\Infrastructure;

use SuperVMar\Sale\Domain\Entity\Line;
use SuperVMar\Sale\Domain\Sale;
use SuperVMar\Sale\Domain\Service\BillGenerator;
use SuperVMar\Sale\Domain\ValueObject\PayMethod;
use SuperVMar\Sale\Domain\ValueObject\SaleBill;

final readonly class EscPosBillGenerator implements BillGenerator
{
    private const ESC = "\x1B";
    private const GS = "\x1D";
    private const INIT = "\x1B\x40";
    private const BOLD_ON = "\x1B\x45\x01";
    private const BOLD_OFF = "\x1B\x45\x00";
    private const ALIGN_CENTER = "\x1B\x61\x01";
    private const ALIGN_LEFT = "\x1B\x61\x00";
    private const ALIGN_RIGHT = "\x1B\x61\x02";
    private const DOUBLE_HEIGHT_ON = "\x1B\x21\x10";
    private const DOUBLE_HEIGHT_OFF = "\x1B\x21\x00";
    private const CUT = "\x1D\x56\x42\x00";
    private const FEED_LINE = "\x0A";
    private const TICKET_WIDTH = 48;

    public function __construct(
        private string $projectDir,
        private ?string $printerHost,
        private ?string $printerPort,
    ) {}

    public function generate(Sale $sale): SaleBill
    {
        $data = $this->buildEscPosData($sale);
        $relativePath = $this->saveFile($sale, $data);

        if (!empty($this->printerHost) && !empty($this->printerPort)) {
            $this->printTicket($data);
        }

        return new SaleBill($relativePath);
    }

    private function buildEscPosData(Sale $sale): string
    {
        $separator = str_repeat('-', self::TICKET_WIDTH);
        $finishedDate = $sale->finishedDate();
        $dateStr = $finishedDate->format('d/m/Y H:i');
        $saleIdShort = substr($sale->id()->value(), -8);

        $data = '';
        $data .= self::INIT;

        // Store header
        $data .= self::ALIGN_CENTER;
        $data .= self::DOUBLE_HEIGHT_ON;
        $data .= self::BOLD_ON;
        $data .= 'SUPERUBEMAR' . self::FEED_LINE;
        $data .= self::DOUBLE_HEIGHT_OFF;
        $data .= self::BOLD_OFF;
        $data .= self::FEED_LINE;
        $data .= 'Carretera de Níjar, 294 - La Cañada, Almería' . self::FEED_LINE;
        $data .= 'Tel: 664 788 445' . self::FEED_LINE;
        $data .= self::FEED_LINE;

        $data .= self::BOLD_ON;
        $data .= 'FACTURA SIMPLIFICADA' . self::FEED_LINE;
        $data .= self::BOLD_OFF;

        $data .= self::ALIGN_LEFT;
        $data .= $separator . self::FEED_LINE;

        // Sale number and date on same line
        $leftPart = 'N' . "\xC2\xBA" . ' Venta: ' . $saleIdShort;
        $spaces = self::TICKET_WIDTH - mb_strlen($leftPart) - mb_strlen($dateStr);
        $data .= $leftPart . str_repeat(' ', max(1, $spaces)) . $dateStr . self::FEED_LINE;

        $data .= $separator . self::FEED_LINE;

        // Column headers
        $data .= self::BOLD_ON;
        $data .= $this->padRight('ARTÍCULO', 16)
            . $this->padLeft('PVP', 6)
            . $this->padLeft('UDS', 4)
            . $this->padLeft('IMPORTE', 8);
        $data .= self::FEED_LINE;
        $data .= self::BOLD_OFF;

        // Lines
        /** @var Line $line */
        foreach ($sale->lines() as $line) {
            $product = $line->product();
            $name = mb_substr($product->name()->value(), 0, 16);
            $price = number_format($product->priceValue(), 2, '.', '');
            $qty = (string) $line->quantity()->value();
            $amount = number_format($line->amount()->value(), 2, '.', '');
            $taxPercent = $product->tax()->percent()->value();

            $lineStr = $this->padRight($name, 16)
                . $this->padLeft($price, 6)
                . $this->padLeft($qty, 4)
                . $this->padLeft($amount, 8);

            if ($taxPercent > 0) {
                $lineStr .= ' (' . (int) $taxPercent . '%)';
            }

            $data .= $lineStr . self::FEED_LINE;
        }

        $data .= $separator . self::FEED_LINE;

        // Totals
        $data .= self::ALIGN_RIGHT;
        $amountStr = number_format($sale->amount()->value(), 2, '.', '') . "\xE2\x82\xAC";
        $taxesStr = number_format($sale->taxesAmount()->value(), 2, '.', '') . "\xE2\x82\xAC";
        $totalStr = number_format($sale->totalAmount()->value(), 2, '.', '') . "\xE2\x82\xAC";

        $data .= 'Base imponible: ' . $amountStr . self::FEED_LINE;
        $data .= 'IVA: ' . $taxesStr . self::FEED_LINE;
        $data .= self::BOLD_ON;
        $data .= 'TOTAL: ' . $totalStr . self::FEED_LINE;
        $data .= self::BOLD_OFF;

        $data .= self::FEED_LINE;
        $data .= self::ALIGN_LEFT;
        $payMethodLabel = match ($sale->payMethod()) {
            PayMethod::CARD => 'Tarjeta',
            PayMethod::CASH => 'Efectivo',
            PayMethod::NONE => '-',
        };
        $data .= 'Metodo de pago: ' . $payMethodLabel . self::FEED_LINE;

        $data .= self::ALIGN_CENTER;
        $data .= self::FEED_LINE;
        $data .= "\xC2\xA1" . '¡Gracias por su compra!' . self::FEED_LINE;
        $data .= 'Conserve este ticket' . self::FEED_LINE;
        $data .= self::FEED_LINE;
        $data .= self::FEED_LINE;
        $data .= self::FEED_LINE;
        $data .= self::CUT;

        return $data;
    }

    private function saveFile(Sale $sale, string $data): string
    {
        $finishedDate = $sale->finishedDate();
        $year = $finishedDate->format('Y');
        $month = $finishedDate->format('m');
        $dateForName = $finishedDate->format('Y-m-d_H-i-s');
        $saleId = $sale->id()->value();

        $dir = $this->projectDir . '/public/documents/bills/' . $year . '/' . $month;
        mkdir($dir, 0755, true);

        $filename = $saleId . '-' . $dateForName . '.bin';
        $fullPath = $dir . '/' . $filename;
        file_put_contents($fullPath, $data);

        return 'documents/bills/' . $year . '/' . $month . '/' . $filename;
    }

    private function printTicket(string $data): void
    {
        $socket = @fsockopen(
            $this->printerHost,
            (int) $this->printerPort,
            $errno,
            $errstr,
            5
        );

        if ($socket === false) {
            return;
        }

        fwrite($socket, $data);
        fclose($socket);
    }

    private function padRight(string $input, int $length): string
    {
        $inputLen = mb_strlen($input);
        if ($inputLen >= $length) {
            return mb_substr($input, 0, $length);
        }

        return $input . str_repeat(' ', $length - $inputLen);
    }

    private function padLeft(string $input, int $length): string
    {
        $inputLen = mb_strlen($input);
        if ($inputLen >= $length) {
            return mb_substr($input, 0, $length);
        }

        return str_repeat(' ', $length - $inputLen) . $input;
    }
}
