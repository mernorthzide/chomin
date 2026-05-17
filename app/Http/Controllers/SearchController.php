<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SearchController extends Controller
{
    public function autocomplete(string $locale, Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['products' => [], 'collections' => []]);
        }

        $like = '%'.$q.'%';

        $products = Product::active()
            ->with(['primaryImage', 'translations', 'collection.translations'])
            ->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhereHas('translations', fn ($q) => $q->where('name', 'like', $like))
                    ->orWhereHas('collection.translations', fn ($q) => $q->where('name', 'like', $like));
            })
            ->limit(6)
            ->get()
            ->map(fn (Product $p) => [
                'id' => $p->id,
                'name' => $p->localized_name,
                'collection' => $p->collection?->localized_name,
                'price' => (float) $p->display_price,
                'image' => $p->primaryImage ? Storage::url($p->primaryImage->image_path) : null,
                'url' => route('products.show', ['locale' => $locale, 'product' => $p->slug]),
            ]);

        $collections = Collection::active()
            ->with('translations')
            ->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhereHas('translations', fn ($q) => $q->where('name', 'like', $like));
            })
            ->limit(3)
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->localized_name ?? $c->name,
                'url' => route('collections.show', ['locale' => $locale, 'collection' => $c->slug]),
            ]);

        return response()->json([
            'products' => $products,
            'collections' => $collections,
            'view_all_url' => route('search', ['locale' => $locale, 'q' => $q]),
        ]);
    }

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

        return view('pages.search', compact('q', 'products'));
    }
}
