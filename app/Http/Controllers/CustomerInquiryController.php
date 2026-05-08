<?php

namespace App\Http\Controllers;

use App\Mail\CustomerInquiryReceived;
use App\Models\CustomerInquiry;
use App\Models\SiteSetting;
use App\Support\SafeMail;
use Illuminate\Http\Request;

class CustomerInquiryController extends Controller
{
    public function store(Request $request, string $locale, string $type = 'contact')
    {
        abort_unless(in_array($type, ['contact', 'careers', 'partnerships', 'wholesale'], true), 404);

        if ($request->filled('company')) {
            return back();
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'topic' => ['nullable', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $inquiry = CustomerInquiry::create($data + [
            'type' => $type,
            'locale' => $locale,
            'status' => 'new',
            'meta' => ['path' => $request->path()],
        ]);

        $adminEmail = SiteSetting::get('inquiry_notification_email') ?: SiteSetting::get('site_email');
        SafeMail::queue($adminEmail, new CustomerInquiryReceived($inquiry));

        return back()->with('success', $locale === 'en' ? 'Message received.' : 'รับข้อความเรียบร้อยแล้ว');
    }
}
