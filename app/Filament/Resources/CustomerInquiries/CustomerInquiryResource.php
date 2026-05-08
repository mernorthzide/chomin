<?php

namespace App\Filament\Resources\CustomerInquiries;

use App\Filament\Resources\CustomerInquiries\Pages\EditCustomerInquiry;
use App\Filament\Resources\CustomerInquiries\Pages\ListCustomerInquiries;
use App\Models\CustomerInquiry;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CustomerInquiryResource extends Resource
{
    protected static ?string $model = CustomerInquiry::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string|\UnitEnum|null $navigationGroup = 'ลูกค้า';

    protected static ?string $navigationLabel = 'Customer inquiries';

    protected static ?string $modelLabel = 'Customer inquiry';

    protected static ?string $pluralModelLabel = 'Customer inquiries';

    protected static ?int $navigationSort = 21;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('ข้อมูลลูกค้า')
                ->schema([
                    Select::make('type')->label('ประเภท')->options([
                        'contact' => 'Contact',
                        'careers' => 'Careers',
                        'partnerships' => 'Partnerships',
                        'wholesale' => 'Wholesale',
                    ])->required(),
                    Select::make('locale')->label('ภาษา')->options(config('chomin.locales.labels'))->required(),
                    Select::make('status')->label('สถานะ')->options([
                        'new' => 'New',
                        'in_progress' => 'In progress',
                        'closed' => 'Closed',
                    ])->required(),
                    TextInput::make('name')->label('ชื่อ')->required(),
                    TextInput::make('email')->label('Email')->email()->required(),
                    TextInput::make('phone')->label('โทร'),
                    TextInput::make('topic')->label('หัวข้อ')->columnSpanFull(),
                    Textarea::make('message')->label('ข้อความ')->required()->columnSpanFull(),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label('วันที่')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('type')->label('ประเภท')->badge()->sortable(),
                TextColumn::make('status')->label('สถานะ')->badge()->sortable(),
                TextColumn::make('name')->label('ชื่อ')->searchable(),
                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('topic')->label('หัวข้อ')->limit(30)->searchable(),
            ])
            ->filters([
                SelectFilter::make('type')->options([
                    'contact' => 'Contact',
                    'careers' => 'Careers',
                    'partnerships' => 'Partnerships',
                    'wholesale' => 'Wholesale',
                ]),
                SelectFilter::make('status')->options([
                    'new' => 'New',
                    'in_progress' => 'In progress',
                    'closed' => 'Closed',
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
            'index' => ListCustomerInquiries::route('/'),
            'edit' => EditCustomerInquiry::route('/{record}/edit'),
        ];
    }
}
