<?php
namespace Tests\Unit\Models;
use App\Models\Order;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function test_status_label_returns_thai_text(): void
    {
        $order = new Order(['status' => 'pending']);
        $this->assertEquals('รอชำระเงิน', $order->status_label);

        $order = new Order(['status' => 'completed']);
        $this->assertEquals('สำเร็จ', $order->status_label);

        $order = new Order(['status' => 'shipping']);
        $this->assertEquals('กำลังจัดส่ง', $order->status_label);
    }
}
