<?php
namespace Tests\Unit\Models;
use App\Models\Coupon;
use PHPUnit\Framework\TestCase;

class CouponTest extends TestCase
{
    public function test_fixed_coupon_calculates_discount(): void
    {
        $coupon = new Coupon(['type' => 'fixed', 'value' => 100]);
        $this->assertEquals(100.00, $coupon->calculateDiscount(500));
    }

    public function test_fixed_coupon_does_not_exceed_subtotal(): void
    {
        $coupon = new Coupon(['type' => 'fixed', 'value' => 200]);
        $this->assertEquals(150.00, $coupon->calculateDiscount(150));
    }

    public function test_percent_coupon_calculates_discount(): void
    {
        $coupon = new Coupon(['type' => 'percent', 'value' => 10]);
        $this->assertEquals(50.00, $coupon->calculateDiscount(500));
    }

    public function test_percent_coupon_respects_max_discount(): void
    {
        $coupon = new Coupon(['type' => 'percent', 'value' => 50, 'max_discount' => 100]);
        $this->assertEquals(100.00, $coupon->calculateDiscount(500));
    }

    public function test_inactive_coupon_is_invalid(): void
    {
        $coupon = new Coupon(['is_active' => false]);
        $this->assertFalse($coupon->isValid());
    }

    public function test_coupon_below_min_order_is_invalid(): void
    {
        $coupon = new Coupon(['is_active' => true, 'min_order_amount' => 500]);
        $this->assertFalse($coupon->isValid(200));
    }
}
