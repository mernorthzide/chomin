<?php

namespace App\Filament\Resources\CookieConsents;

use App\Filament\Resources\CookieConsents\Pages\ListCookieConsents;
use App\Models\CookieConsent;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CookieConsentResource extends Resource
{
    protected static ?string $model = CookieConsent::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'ลูกค้า';

    protected static ?string $navigationLabel = 'Cookie consent';

    protected static ?string $modelLabel = 'Cookie consent';

    protected static ?string $pluralModelLabel = 'Cookie consent';

    protected static ?int $navigationSort = 22;

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('consented_at')->label('วันที่')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('locale')->label('ภาษา')->badge(),
                TextColumn::make('consent_id')->label('Consent ID')->searchable()->limit(18),
                TextColumn::make('categories')->label('Categories')->formatStateUsing(fn ($state): string => collect($state)->filter()->keys()->implode(', ')),
                TextColumn::make('ip_hash')->label('IP hash')->limit(12),
            ])
            ->defaultSort('consented_at', 'desc')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCookieConsents::route('/'),
        ];
    }
}
