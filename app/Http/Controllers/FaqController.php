<?php

namespace App\Http\Controllers;

use App\Models\FaqItem;

class FaqController extends Controller
{
    public function __invoke()
    {
        $items = FaqItem::where('is_published', true)
            ->with('translations')
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        return view('pages.faq', [
            'items' => $items,
            'title' => app()->getLocale() === 'en' ? 'FAQ | CHOMIN' : 'คำถามที่พบบ่อย | CHOMIN',
        ]);
    }
}
