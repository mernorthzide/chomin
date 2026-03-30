<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Collection;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $banners = Banner::active()->ordered()->get();

        $collections = Collection::active()
            ->ordered()
            ->with(['products' => function ($query) {
                $query->active()
                    ->with(['primaryImage', 'images', 'variants'])
                    ->orderBy('sort_order')
                    ->limit(6);
            }])
            ->get();

        $quote = SiteSetting::get('homepage_quote', '"ความงามที่แท้จริงอยู่ในความเรียบง่าย"');

        return view('pages.home', compact('banners', 'collections', 'quote'));
    }
}
