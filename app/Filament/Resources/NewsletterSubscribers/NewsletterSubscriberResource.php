<?php

namespace App\Filament\Resources\NewsletterSubscribers;

use App\Filament\Resources\NewsletterSubscribers\Pages\ListNewsletterSubscribers;
use App\Filament\Resources\NewsletterSubscribers\Pages\EditNewsletterSubscriber;
use App\Models\NewsletterSubscriber;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class NewsletterSubscriberResource extends Resource
{
    protected static ?string $model = NewsletterSubscriber::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static string|\UnitEnum|null $navigationGroup = 'ลูกค้า';

    protected static ?string $navigationLabel = 'Newsletter';

    protected static ?string $modelLabel = 'Newsletter subscriber';

    protected static ?string $pluralModelLabel = 'Newsletter subscribers';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('email')->label('Email')->email()->required(),
            Select::make('locale')->label('ภาษา')->options(config('chomin.locales.labels'))->required(),
            Select::make('status')->label('สถานะ')->options([
                'subscribed' => 'Subscribed',
                'unsubscribed' => 'Unsubscribed',
            ])->required(),
            TextInput::make('source')->label('ที่มา')->default('footer'),
            DateTimePicker::make('subscribed_at')->label('สมัครเมื่อ'),
            DateTimePicker::make('unsubscribed_at')->label('ยกเลิกเมื่อ'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')->label('Email')->searchable()->sortable(),
                TextColumn::make('locale')->label('ภาษา')->badge(),
                TextColumn::make('status')->label('สถานะ')->badge()->sortable(),
                TextColumn::make('source')->label('ที่มา')->sortable(),
                TextColumn::make('subscribed_at')->label('สมัครเมื่อ')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'subscribed' => 'Subscribed',
                    'unsubscribed' => 'Unsubscribed',
                ]),
                SelectFilter::make('locale')->options(config('chomin.locales.labels')),
            ])
            ->defaultSort('subscribed_at', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNewsletterSubscribers::route('/'),
            'edit' => EditNewsletterSubscriber::route('/{record}/edit'),
        ];
    }
}
