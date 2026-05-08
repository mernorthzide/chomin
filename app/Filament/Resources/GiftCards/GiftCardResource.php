<?php

namespace App\Filament\Resources\GiftCards;

use App\Filament\Resources\GiftCards\Pages\CreateGiftCard;
use App\Filament\Resources\GiftCards\Pages\EditGiftCard;
use App\Filament\Resources\GiftCards\Pages\ListGiftCards;
use App\Models\GiftCard;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GiftCardResource extends Resource
{
    protected static ?string $model = GiftCard::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-gift';

    protected static string|\UnitEnum|null $navigationGroup = 'การขาย';

    protected static ?string $navigationLabel = 'Gift cards';

    protected static ?string $modelLabel = 'Gift card';

    protected static ?string $pluralModelLabel = 'Gift cards';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('ออกบัตรของขวัญ')
                ->schema([
                    TextInput::make('plain_code')
                        ->label('รหัสเต็ม')
                        ->helperText('เว้นว่างเพื่อให้ระบบสุ่มให้ รหัสเต็มจะแสดงเฉพาะหลังสร้างบัตรครั้งแรก')
                        ->visible(fn (string $operation): bool => $operation === 'create'),
                    TextInput::make('initial_balance')
                        ->label('มูลค่าเริ่มต้น')
                        ->numeric()
                        ->prefix('฿')
                        ->required()
                        ->disabled(fn (string $operation): bool => $operation === 'edit'),
                    TextInput::make('balance')
                        ->label('ยอดคงเหลือ')
                        ->numeric()
                        ->prefix('฿')
                        ->visible(fn (string $operation): bool => $operation === 'edit'),
                    Select::make('status')
                        ->label('สถานะ')
                        ->options([
                            'active' => 'Active',
                            'disabled' => 'Disabled',
                            'redeemed' => 'Redeemed',
                            'expired' => 'Expired',
                        ])
                        ->default('active')
                        ->required(),
                    TextInput::make('recipient_email')->label('อีเมลผู้รับ')->email(),
                    TextInput::make('recipient_name')->label('ชื่อผู้รับ'),
                    DateTimePicker::make('expires_at')->label('หมดอายุ'),
                    Textarea::make('message')->label('ข้อความ')->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code_last4')->label('ท้ายรหัส')->searchable(),
                TextColumn::make('initial_balance')->label('มูลค่า')->money('THB')->sortable(),
                TextColumn::make('balance')->label('คงเหลือ')->money('THB')->sortable(),
                TextColumn::make('status')->label('สถานะ')->badge()->sortable(),
                TextColumn::make('recipient_email')->label('ผู้รับ')->searchable(),
                TextColumn::make('expires_at')->label('หมดอายุ')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('created_at')->label('สร้างเมื่อ')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'active' => 'Active',
                    'disabled' => 'Disabled',
                    'redeemed' => 'Redeemed',
                    'expired' => 'Expired',
                ]),
            ])
            ->defaultSort('created_at', 'desc')
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
            'index' => ListGiftCards::route('/'),
            'create' => CreateGiftCard::route('/create'),
            'edit' => EditGiftCard::route('/{record}/edit'),
        ];
    }
}
