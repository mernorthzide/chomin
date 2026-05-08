<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
        $description  = Str::of($aboutContent)
            ->stripTags()
            ->squish()
            ->limit(160)
            ->toString();
        $description = $description !== ''
            ? $description
            : 'เรื่องราวของ CHOMIN แบรนด์เชิ้ตไทยที่ให้คุณเลือกสีและไซส์ได้อย่างอิสระ';
        $title = "{$aboutTitle} | CHOMIN";
        $ogImage = $aboutImage ? url(Storage::url($aboutImage)) : null;

        return view('pages.about', compact('aboutContent', 'aboutTitle', 'aboutImage', 'title', 'description', 'ogImage'));
    }
}
