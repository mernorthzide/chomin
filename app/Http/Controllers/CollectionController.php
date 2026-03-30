<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    /**
     * Display all active collections.
     */
    public function index()
    {
        $collections = Collection::active()
            ->ordered()
            ->withCount(['products' => fn($q) => $q->active()])
            ->get();

        return view('pages.collections.index', compact('collections'));
    }

    /**
     * Display products in a specific collection.
     */
    public function show(Request $request, Collection $collection)
    {
        abort_unless($collection->is_active, 404);

        $query = $collection->products()
            ->active()
            ->with(['primaryImage', 'images', 'variants', 'category']);

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
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

        $categories = Category::active()->ordered()->get();

        return view('pages.collections.show', compact('collection', 'products', 'categories', 'sort'));
    }
}
