<?php

namespace App\Service;

class StockManager
{
    public function calculateTTC(float $priceHt, float $vatRate = 0.20): float
    {
        return $priceHt * (1 + $vatRate);
    }

    public function canSell(int $currentStock, int $requestedQty): bool
    {
        return $currentStock >= $requestedQty;
    }
}