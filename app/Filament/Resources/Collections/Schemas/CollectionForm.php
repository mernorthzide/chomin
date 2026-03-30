<?php

namespace App\Filament\Resources\Collections\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CollectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('ชื่อคอลเล็คชัน')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $operation, $state, \Filament\Forms\Set $set) =>
                        $operation === 'create' ? $set('slug', Str::slug($state)) : null
                    ),
                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->rules(['alpha_dash']),
                Textarea::make('description')
                    ->label('คำอธิบาย')
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->label('รูปภาพ')
                    ->image()
                    ->directory('collections'),
                FileUpload::make('banner_image')
                    ->label('รูปแบนเนอร์')
                    ->image()
                    ->directory('collections/banners'),
                Toggle::make('is_active')
                    ->label('เปิดใช้งาน')
                    ->default(true),
                TextInput::make('sort_order')
                    ->label('ลำดับ')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
