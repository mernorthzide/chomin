<?php

namespace App\Filament\Resources\NewsletterSubscribers\Pages;

use App\Exports\NewsletterSubscribersExport;
use App\Filament\Resources\NewsletterSubscribers\NewsletterSubscriberResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListNewsletterSubscribers extends ListRecords
{
    protected static string $resource = NewsletterSubscriberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => Excel::download(new NewsletterSubscribersExport, 'newsletter-subscribers.xlsx')),
        ];
    }
}
