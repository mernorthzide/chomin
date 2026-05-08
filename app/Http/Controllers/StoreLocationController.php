<?php

namespace App\Http\Controllers;

use App\Models\StoreLocation;

class StoreLocationController extends Controller
{
    public function __invoke()
    {
        $locations = StoreLocation::where('is_active', true)
            ->with('translations')
            ->orderBy('sort_order')
            ->get();

        return view('pages.stores', compact('locations'));
    }
}
