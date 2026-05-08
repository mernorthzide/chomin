<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $products = Product::query()
            ->active()
            ->with(['translations', 'primaryImage', 'images', 'variants', 'colors.translations', 'category.translations', 'collection.translations'])
            ->when(mb_strlen($q) >= 2, function ($query) use ($q) {
                $like = '%'.$q.'%';

                $query->where(function ($query) use ($like) {
                    $query->where('name', 'like', $like)
                        ->orWhere('description', 'like', $like)
                        ->orWhereHas('translations', fn ($query) => $query
                            ->where('name', 'like', $like)
                            ->orWhere('description', 'like', $like))
                        ->orWhereHas('category', fn ($query) => $query
                            ->where('name', 'like', $like)
                            ->orWhereHas('translations', fn ($query) => $query->where('name', 'like', $like)))
                        ->orWhereHas('collection', fn ($query) => $query
                            ->where('name', 'like', $like)
                            ->orWhere('description', 'like', $like)
                            ->orWhereHas('translations', fn ($query) => $query
                                ->where('name', 'like', $like)
                                ->orWhere('description', 'like', $like)));
                });
            }, fn ($query) => $query->whereRaw('1 = 0'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        if (mb_strlen($q) >= 2 && $products->total() === 0) {
            $needle = mb_strtolower($q);
            $matches = Product::active()
                ->with(['translations', 'primaryImage', 'images', 'variants', 'colors.translations', 'category.translations', 'collection.translations'])
                ->get()
                ->filter(function (Product $product) use ($needle) {
                    $haystack = collect([
                        $product->name,
                        $product->description,
                        $product->category?->name,
                        $product->collection?->name,
                    ])
                        ->merge($product->translations->flatMap(fn ($translation) => [$translation->name, $translation->description]))
                        ->merge($product->category?->translations?->pluck('name') ?? [])
                        ->merge($product->collection?->translations?->flatMap(fn ($translation) => [$translation->name, $translation->description]) ?? [])
                        ->filter()
                        ->map(fn ($value) => mb_strtolower((string) $value))
                        ->implode(' ');

                    return mb_stripos($haystack, $needle) !== false;
                })
                ->values();

            $page = LengthAwarePaginator::resolveCurrentPage();
            $products = new LengthAwarePaginator(
                $matches->forPage($page, 12),
                $matches->count(),
                12,
                $page,
                ['path' => request()->url(), 'query' => request()->query()],
            );
        }

        return view('pages.search', compact('q', 'products'));
    }
}
