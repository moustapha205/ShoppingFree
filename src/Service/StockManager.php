<?php

namespace App\Service;

class StockManager
{
    public function calculateTTC(float $priceHt, float $vatRate = 0.20): float // mes propres services que j'ai cree en fonction de mon projet pour voir si le stock est suffisant et pour calculer le ttc 
    {
        return $priceHt * (1 + $vatRate);
    }

    public function canSell(int $currentStock, int $requestedQty): bool
    {
        return $currentStock >= $requestedQty;
    }
}