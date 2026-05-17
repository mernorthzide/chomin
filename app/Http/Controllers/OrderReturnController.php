<?php

namespace App\Http\Controllers;

use App\Enums\OrderReturnStatus;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderReturn;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderReturnController extends Controller
{
    private function eligibleDays(): int
    {
        return (int) config('chomin.returns.eligible_days', 30);
    }

    private function eligibleStatuses(): array
    {
        return OrderStatus::paidStatuses();
    }

    public function index(string $locale)
    {
        $returns = OrderReturn::where('user_id', Auth::id())
            ->with('order')
            ->latest()
            ->paginate(10);

        return view('pages.profile.returns.index', compact('returns'));
    }

    public function create(string $locale, Order $order)
    {
        abort_unless($order->user_id === Auth::id(), 403);
        abort_unless($this->isEligible($order), 422);

        $order->load('items.product.primaryImage', 'items.variant.color.translations');

        $hasExistingReturn = OrderReturn::where('order_id', $order->id)
            ->whereNotIn('status', OrderReturnStatus::closedStatuses())
            ->exists();

        if ($hasExistingReturn) {
            return redirect()->route('returns.index', ['locale' => $locale])
                ->with('flash', [
                    'type' => 'info',
                    'message' => $locale === 'en'
                        ? 'You already have an active return for this order.'
                        : 'ออเดอร์นี้มีคำขอคืนที่กำลังดำเนินอยู่แล้ว',
                ]);
        }

        return view('pages.profile.returns.create', compact('order'));
    }

    public function store(Request $request, string $locale, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === Auth::id(), 403);
        abort_unless($this->isEligible($order), 422);

        $data = $request->validate([
            'type' => ['required', 'in:return,exchange'],
            'reason' => ['required', 'in:size_too_small,size_too_large,color_different,defective,not_as_described,changed_mind,other'],
            'reason_detail' => ['nullable', 'string', 'max:1000'],
            'item_ids' => ['required', 'array', 'min:1'],
            'item_ids.*' => ['integer', 'exists:order_items,id'],
            'photos' => ['nullable', 'array', 'max:6'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $items = $order->items()
            ->whereIn('id', $data['item_ids'])
            ->get()
            ->map(fn ($item) => [
                'order_item_id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->product_name,
                'size' => $item->size,
                'color' => $item->color_name,
                'quantity' => $item->quantity,
                'price' => (float) $item->price,
            ])
            ->all();

        if (empty($items)) {
            return back()->withInput()->withErrors(['item_ids' => 'No items selected.']);
        }

        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $photoPaths[] = $photo->store("returns/{$order->id}", 'public');
            }
        }

        $refundAmount = $data['type'] === 'return'
            ? collect($items)->sum(fn ($i) => $i['price'] * $i['quantity'])
            : 0;

        $return = OrderReturn::create([
            'rma_number' => OrderReturn::generateRmaNumber(),
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'type' => $data['type'],
            'reason' => $data['reason'],
            'reason_detail' => $data['reason_detail'] ?? null,
            'items' => $items,
            'photos' => $photoPaths ?: null,
            'status' => 'requested',
            'refund_amount' => $refundAmount,
        ]);

        return redirect()->route('returns.show', ['locale' => $locale, 'return' => $return->id])
            ->with('flash', [
                'type' => 'success',
                'message' => $locale === 'en'
                    ? "Return request {$return->rma_number} submitted. We'll review it shortly."
                    : "ส่งคำขอคืนสินค้า {$return->rma_number} เรียบร้อย ทีมงานจะตรวจสอบและติดต่อกลับ",
            ]);
    }

    public function show(string $locale, OrderReturn $return)
    {
        abort_unless($return->user_id === Auth::id(), 403);

        $return->load('order');

        return view('pages.profile.returns.show', compact('return'));
    }

    public function cancel(string $locale, OrderReturn $return): RedirectResponse
    {
        abort_unless($return->user_id === Auth::id(), 403);
        abort_unless(in_array($return->status, OrderReturnStatus::openStatuses()), 422);

        $return->update(['status' => OrderReturnStatus::Cancelled->value]);

        return back()->with('flash', [
            'type' => 'success',
            'message' => $locale === 'en' ? 'Return cancelled.' : 'ยกเลิกคำขอคืนแล้ว',
        ]);
    }

    public static function isEligible(Order $order): bool
    {
        if (! in_array($order->status, OrderStatus::paidStatuses())) {
            return false;
        }

        $anchor = $order->shipped_at ?? $order->created_at;

        return $anchor->gte(now()->subDays((int) config('chomin.returns.eligible_days', 30)));
    }
}
