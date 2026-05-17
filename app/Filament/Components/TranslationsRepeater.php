<?php

namespace App\Filament\Components;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class TranslationsRepeater
{
    /**
     * Build a translations Repeater for any model that uses
     * the HasLocalizedContent trait. Pass the translatable fields
     * you want to expose, e.g. ['name', 'description', 'seo_title'].
     */
    public static function make(array $fields = ['name', 'description'], string $relationship = 'translations'): Repeater
    {
        $schema = [
            Select::make('locale')
                ->label('ภาษา')
                ->options(config('chomin.locales.labels'))
                ->required(),
        ];

        foreach ($fields as $field) {
            $schema[] = self::componentFor($field);
        }

        return Repeater::make($relationship)
            ->label('Translations')
            ->relationship()
            ->schema($schema)
            ->columns(2)
            ->defaultItems(2)
            ->columnSpanFull();
    }

    private static function componentFor(string $field)
    {
        return match (true) {
            str_contains($field, 'description') => Textarea::make($field)
                ->label(self::labelFor($field))
                ->columnSpanFull(),
            default => TextInput::make($field)->label(self::labelFor($field)),
        };
    }

    private static function labelFor(string $field): string
    {
        return match ($field) {
            'name' => 'ชื่อ',
            'description' => 'คำอธิบาย',
            'seo_title' => 'SEO title',
            'seo_description' => 'SEO description',
            'title' => 'หัวข้อ',
            'subtitle' => 'หัวข้อย่อย',
            'body' => 'เนื้อหา',
            default => ucfirst(str_replace('_', ' ', $field)),
        };
    }
}
