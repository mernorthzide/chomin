<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $query = Product::active()
            ->with(['primaryImage', 'images', 'variants', 'category', 'collection']);

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        // Filter by collection
        if ($request->filled('collection')) {
            $query->whereHas('collection', fn($q) => $q->where('slug', $request->collection));
        }

        // Sort
        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc'   => $query->orderBy('name', 'asc'),
            default      => $query->orderBy('created_at', 'desc'),
        };

        $products = $query->paginate(12)->withQueryString();

        $categories   = Category::active()->ordered()->get();
        $collections  = Collection::active()->ordered()->get();

        return view('pages.shop', compact('products', 'categories', 'collections', 'sort'));
    }
}
