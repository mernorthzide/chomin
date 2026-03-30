<?php

namespace App\Filament\Resources\Banners\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BannerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('หัวข้อ'),
                TextInput::make('subtitle')
                    ->label('คำบรรยาย'),
                FileUpload::make('image')
                    ->label('รูปภาพ')
                    ->image()
                    ->directory('banners')
                    ->required(),
                TextInput::make('link')
                    ->label('ลิงก์')
                    ->url(),
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
