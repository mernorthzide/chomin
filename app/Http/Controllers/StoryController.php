<?php

namespace App\Http\Controllers;

use App\Models\Story;

class StoryController extends Controller
{
    public function index()
    {
        $stories = Story::published()
            ->with('translations')
            ->orderByDesc('published_at')
            ->orderBy('sort_order')
            ->paginate(9);

        return view('pages.stories.index', compact('stories'));
    }

    public function show(string $locale, Story $story)
    {
        abort_unless($story->is_published, 404);
        $story->load('translations');
        $title = $story->localized('seo_title') ?: $story->localized('title');
        if (! str_contains($title, 'CHOMIN')) {
            $title .= ' | CHOMIN';
        }

        return view('pages.stories.show', [
            'story' => $story,
            'title' => $title,
            'description' => $story->localized('seo_description') ?: $story->localized('excerpt'),
        ]);
    }
}
