<?php

namespace App\Exports;

use App\Models\NewsletterSubscriber;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class NewsletterSubscribersExport implements FromQuery, WithHeadings, WithMapping
{
    public function query()
    {
        return NewsletterSubscriber::query()->orderByDesc('subscribed_at')->orderByDesc('created_at');
    }

    public function headings(): array
    {
        return ['Email', 'Locale', 'Status', 'Source', 'Subscribed At', 'Unsubscribed At'];
    }

    public function map($subscriber): array
    {
        return [
            $subscriber->email,
            $subscriber->locale,
            $subscriber->status,
            $subscriber->source,
            $subscriber->subscribed_at?->format('Y-m-d H:i:s'),
            $subscriber->unsubscribed_at?->format('Y-m-d H:i:s'),
        ];
    }
}
