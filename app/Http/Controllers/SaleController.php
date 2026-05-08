<?php

namespace App\Http\Controllers;

use App\Models\Product;

class SaleController extends Controller
{
    public function __invoke()
    {
        $products = Product::active()
            ->onSale()
            ->with(['translations', 'primaryImage', 'images', 'variants', 'colors.translations'])
            ->orderBy('sale_ends_at')
            ->paginate(12);

        return view('pages.sale', compact('products'));
    }
}
