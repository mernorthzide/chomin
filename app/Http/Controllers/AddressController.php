<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = auth()->user()->addresses()->orderByDesc('is_default')->get();
        return view('pages.profile.addresses', compact('addresses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'phone'       => 'required|string|max:20',
            'address'     => 'required|string',
            'district'    => 'required|string|max:255',
            'province'    => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'is_default'  => 'nullable|boolean',
        ]);

        $data['user_id'] = auth()->id();

        if (!empty($data['is_default'])) {
            auth()->user()->addresses()->update(['is_default' => false]);
        }

        auth()->user()->addresses()->create($data);

        return back()->with('success', 'เพิ่มที่อยู่เรียบร้อย');
    }

    public function update(Request $request, \App\Models\Address $address)
    {
        abort_unless($address->user_id === auth()->id(), 403);

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'phone'       => 'required|string|max:20',
            'address'     => 'required|string',
            'district'    => 'required|string|max:255',
            'province'    => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'is_default'  => 'nullable|boolean',
        ]);

        if (!empty($data['is_default'])) {
            auth()->user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($data);

        return back()->with('success', 'แก้ไขที่อยู่เรียบร้อย');
    }

    public function destroy(\App\Models\Address $address)
    {
        abort_unless($address->user_id === auth()->id(), 403);
        $address->delete();
        return back()->with('success', 'ลบที่อยู่เรียบร้อย');
    }
}
