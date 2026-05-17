<?php

namespace App\Http\Controllers;

use App\Models\ShippingSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function lookup(Request $request, string $locale): JsonResponse
    {
        $data = $request->validate([
            'postal_code' => ['required', 'string', 'regex:/^\d{5}$/'],
            'subtotal' => ['nullable', 'numeric', 'min:0'],
        ]);

        $postal = $data['postal_code'];
        $prefix = substr($postal, 0, 2);
        $province = config("thai-locations.postal_prefix_to_province.{$prefix}");
        $zones = config('thai-locations.shipping_zones');
        $zone = null;
        foreach ($zones as $name => $prefixes) {
            if (in_array($prefix, $prefixes, true)) {
                $zone = $name;
                break;
            }
        }

        $shipping = ShippingSetting::current();
        $subtotal = (float) ($data['subtotal'] ?? 0);
        $shippingFee = $shipping->getShippingFeeFor($subtotal);

        if ($shippingFee > 0 && $zone) {
            $surcharge = (float) (config("chomin.shipping.zone_surcharge.{$zone}", 0));
            $shippingFee += $surcharge;
        }

        $freeShippingThreshold = $shipping->free_shipping_min_amount;
        $amountToFreeShipping = $freeShippingThreshold ? max(0, (float) $freeShippingThreshold - $subtotal) : null;

        return response()->json([
            'ok' => $province !== null,
            'postal_code' => $postal,
            'province' => $province,
            'zone' => $zone,
            'shipping_fee' => $shippingFee,
            'free_shipping_threshold' => $freeShippingThreshold,
            'amount_to_free_shipping' => $amountToFreeShipping,
            'estimated_delivery_days' => $this->estimateDeliveryDays($zone),
        ]);
    }

    private function estimateDeliveryDays(?string $zone): array
    {
        return match ($zone) {
            'bangkok_metro' => ['min' => 1, 'max' => 2],
            'central', 'east', 'west' => ['min' => 2, 'max' => 3],
            'north', 'lower_north', 'northeast' => ['min' => 2, 'max' => 4],
            'south' => ['min' => 3, 'max' => 5],
            default => ['min' => 2, 'max' => 5],
        };
    }
}
