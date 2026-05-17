<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CollectionController extends Controller
{
    /**
     * Display all active collections.
     */
    public function index()
    {
        $collections = Collection::active()
            ->ordered()
            ->with('translations')
            ->withCount(['products' => fn ($q) => $q->active()])
            ->get();

        return view('pages.collections.index', compact('collections'));
    }

    /**
     * Display products in a specific collection.
     */
    public function show(Request $request, string $locale, Collection $collection)
    {
        abort_unless($collection->is_active, 404);

        $collection->loadMissing('translations');

        $query = $collection->products()
            ->active()
            ->with(['primaryImage', 'images', 'variants', 'category.translations', 'collection.translations', 'translations', 'colors.translations']);

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        // Sort
        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        $products = $query->paginate(12)->withQueryString();

        $categories = Category::active()->ordered()->with('translations')->get();
        $title = $collection->localized_name.' | CHOMIN';
        $description = $collection->localized_description
            ?: 'CHO.MIN — เชิ้ตดีไซน์ 50+ สี ไซส์ XS-6XL จัดส่งฟรีทั่วประเทศ';
        $imagePath = $collection->banner_image ?: $collection->image;
        $ogImage = $imagePath ? url(Storage::url($imagePath)) : null;

        return view('pages.collections.show', compact('collection', 'products', 'categories', 'sort', 'title', 'description', 'ogImage'));
    }
}
