<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request)
    {
        $collections = Collection::active()
            ->ordered()
            ->with(['products' => function ($query) {
                $query->active()
                    ->with(['primaryImage', 'images', 'variants'])
                    ->orderBy('sort_order')
                    ->limit(6);
            }])
            ->get();

        $quote = SiteSetting::get('homepage_quote', '"CHO.MIN คือความเรียบที่ไม่ธรรมดา คือความมั่นใจที่คุณใส่ได้ทุกวัน"');

        return view('pages.home', compact('collections', 'quote'));
    }
}
