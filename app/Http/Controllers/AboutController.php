<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $aboutContent = SiteSetting::get('about_content', '');
        $aboutTitle   = SiteSetting::get('about_title', 'เกี่ยวกับ CHOMIN');
        $aboutImage   = SiteSetting::get('about_image', '');

        return view('pages.about', compact('aboutContent', 'aboutTitle', 'aboutImage'));
    }
}
