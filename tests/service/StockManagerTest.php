<?php

namespace App\Tests\Service;

use App\Service\StockManager;
use PHPUnit\Framework\TestCase;

class StockManagerTest extends TestCase
{
    private StockManager $stockManager;

    protected function setUp(): void
    {
        $this->stockManager = new StockManager();
    }

    // ✅ Stock suffisant → on peut vendre
    public function testCanSellReturnsTrueWhenStockSufficient(): void
    {
        $result = $this->stockManager->canSell(10, 3);
        $this->assertTrue($result);
    }

    // ✅ Stock à zéro → on ne peut pas vendre
    public function testCanSellReturnsFalseWhenStockIsZero(): void
    {
        $result = $this->stockManager->canSell(0, 1);
        $this->assertFalse($result);
    }

    // ✅ Stock insuffisant → on ne peut pas vendre
    public function testCanSellReturnsFalseWhenStockInsufficient(): void
    {
        $result = $this->stockManager->canSell(2, 5);
        $this->assertFalse($result);
    }

    // ✅ Calcul TTC avec TVA 20%
    public function testCalculateTtcWithStandardVat(): void
    {
        $result = $this->stockManager->calculateTTC(100.0, 0.20);
        $this->assertEquals(120.0, $result);
    }

    // ✅ Calcul TTC avec TVA par défaut (20%)
    public function testCalculateTtcUsesDefaultVat(): void
    {
        $result = $this->stockManager->calculateTTC(100.0);
        $this->assertEquals(120.0, $result);
    }

    // ✅ Prix HT à 0 → TTC à 0
    public function testCalculateTtcWithZeroPrice(): void
    {
        $result = $this->stockManager->calculateTTC(0.0);
        $this->assertEquals(0.0, $result);
    }
}