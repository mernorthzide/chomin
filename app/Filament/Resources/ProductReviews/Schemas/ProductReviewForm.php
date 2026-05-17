<?php

namespace App\Filament\Resources\ProductReviews\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProductReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('product.name')
                    ->label('สินค้า')
                    ->disabled(),
                TextInput::make('name')
                    ->label('ผู้รีวิว')
                    ->disabled(),
                TextInput::make('email')
                    ->label('อีเมล')
                    ->disabled(),
                TextInput::make('rating')
                    ->label('คะแนน')
                    ->disabled(),
                TextInput::make('title')
                    ->label('หัวข้อรีวิว')
                    ->disabled(),
                Textarea::make('body')
                    ->label('เนื้อหารีวิว')
                    ->disabled()
                    ->rows(4)
                    ->columnSpanFull(),
                Select::make('status')
                    ->label('สถานะ')
                    ->options([
                        'pending' => 'รอตรวจสอบ',
                        'approved' => 'อนุมัติ',
                        'rejected' => 'ไม่อนุมัติ',
                    ])
                    ->required(),
                DateTimePicker::make('approved_at')
                    ->label('วันที่อนุมัติ'),
                Textarea::make('admin_response')
                    ->label('ตอบกลับจาก Admin')
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }
}
