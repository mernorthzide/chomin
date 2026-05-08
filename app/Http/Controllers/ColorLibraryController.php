<?php

namespace App\Http\Controllers;

use App\Models\ProductColor;

class ColorLibraryController extends Controller
{
    public function __invoke()
    {
        $colors = ProductColor::query()
            ->with(['translations', 'images', 'product.translations'])
            ->whereHas('product', fn ($query) => $query->active())
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->unique(fn ($color) => $color->slug ?: $color->name)
            ->values();

        return view('pages.color-library', compact('colors'));
    }
}
