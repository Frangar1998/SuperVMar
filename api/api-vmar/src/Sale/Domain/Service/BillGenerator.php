<?php

namespace SuperVMar\Sale\Domain\Service;

use SuperVMar\Sale\Domain\Sale;
use SuperVMar\Sale\Domain\ValueObject\SaleBill;

interface BillGenerator
{
    public function generate(Sale $sale): SaleBill;
}
