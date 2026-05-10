<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $query = Product::active()
            ->with(['primaryImage', 'images', 'variants', 'category.translations', 'collection.translations', 'translations', 'colors.translations']);

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        // Filter by collection
        if ($request->filled('collection')) {
            $query->whereHas('collection', fn($q) => $q->where('slug', $request->collection));
        }

        // Filter by color
        if ($request->filled('color')) {
            $color = (string) $request->color;
            $query->whereHas('colors', fn($q) => $q
                ->where('slug', $color)
                ->orWhere('name', $color)
                ->orWhereHas('images', fn ($imageQuery) => $imageQuery
                    ->where('image_path', "products/colors/{$color}.png")));
        }

        // Filter by stocked size
        if ($request->filled('size')) {
            $query->whereHas('variants', fn($q) => $q
                ->where('size', $request->size)
                ->where('stock', '>', 0));
        }

        // Sort
        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc'   => $query->orderBy('name', 'asc'),
            default      => $query->orderBy('sort_order')->orderBy('created_at', 'desc'),
        };

        $products = $query->paginate(12)->withQueryString();

        $categories   = Category::active()->ordered()->with('translations')->get();
        $collections  = Collection::active()->ordered()->with('translations')->get();
        $availableSizes = ProductVariant::query()
            ->where('stock', '>', 0)
            ->select('size')
            ->distinct()
            ->pluck('size')
            ->sortBy(fn ($size) => array_search($size, ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL', '6XL'], true) ?? 99)
            ->values();
        $availableColors = ProductColor::query()
            ->with(['translations', 'images'])
            ->whereHas('product', fn ($query) => $query->active())
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->unique(fn ($color) => $color->filter_key)
            ->take(10)
            ->values();

        return view('pages.shop', compact('products', 'categories', 'collections', 'sort', 'availableSizes', 'availableColors'));
    }
}
