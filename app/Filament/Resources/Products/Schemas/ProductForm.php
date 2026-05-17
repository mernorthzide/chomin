<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Filament\Components\TranslationsRepeater;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Set;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('ข้อมูลสินค้า')
                    ->schema([
                        TextInput::make('name')
                            ->label('ชื่อสินค้า')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null
                            ),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->rules(['alpha_dash']),
                        TextInput::make('price')
                            ->label('ราคา')
                            ->required()
                            ->numeric()
                            ->prefix('฿'),
                        TextInput::make('sale_price')
                            ->label('ราคา Sale')
                            ->numeric()
                            ->prefix('฿')
                            ->nullable(),
                        DateTimePicker::make('sale_starts_at')
                            ->label('เริ่ม Sale')
                            ->nullable(),
                        DateTimePicker::make('sale_ends_at')
                            ->label('สิ้นสุด Sale')
                            ->nullable(),
                        Select::make('collection_id')
                            ->label('คอลเล็คชัน')
                            ->relationship('collection', 'name')
                            ->searchable()
                            ->nullable(),
                        Select::make('category_id')
                            ->label('หมวดหมู่')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->nullable(),
                        RichEditor::make('description')
                            ->label('คำอธิบาย')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('ภาษาและ SEO')
                    ->schema([
                        TranslationsRepeater::make(['name', 'description', 'seo_title', 'seo_description']),
                    ]),
                Section::make('ตั้งค่า')
                    ->schema([
                        Toggle::make('is_active')
                            ->label('เปิดใช้งาน')
                            ->default(true),
                        Toggle::make('is_featured')
                            ->label('สินค้าแนะนำ')
                            ->default(false),
                        TextInput::make('sort_order')
                            ->label('ลำดับ')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(3),
            ]);
    }
}
