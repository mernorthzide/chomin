<?php

namespace App\Http\Controllers;

use App\Models\ContentPage;
use Illuminate\Http\Request;

class ContentPageController extends Controller
{
    public function show(Request $request, string $locale, string $slug)
    {
        $page = ContentPage::published()
            ->where('slug', $slug)
            ->with('translations')
            ->firstOrFail();

        return view('pages.content.show', [
            'page' => $page,
            'title' => $page->localized('seo_title') ?: $page->localized('title'),
            'description' => $page->localized('seo_description') ?: $page->localized('excerpt'),
        ]);
    }
}
