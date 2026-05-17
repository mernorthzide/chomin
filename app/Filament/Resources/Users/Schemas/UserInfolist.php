<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('ข้อมูลสมาชิก')
                    ->schema([
                        TextEntry::make('name')->label('ชื่อ'),
                        TextEntry::make('email')->label('อีเมล'),
                        TextEntry::make('phone')->label('เบอร์โทร')->placeholder('-'),
                        TextEntry::make('points')->label('แต้มสะสม')->numeric(),
                        TextEntry::make('created_at')->label('สมัครเมื่อ')->dateTime('d/m/Y H:i'),
                        TextEntry::make('email_verified_at')->label('ยืนยันอีเมลเมื่อ')->dateTime('d/m/Y H:i')->placeholder('ยังไม่ยืนยัน'),
                    ])
                    ->columns(2),
                Section::make('ระบบแนะนำเพื่อน')
                    ->schema([
                        TextEntry::make('referral_code')
                            ->label('รหัสแนะนำ')
                            ->placeholder('ยังไม่มี')
                            ->copyable(),
                        TextEntry::make('referrer.name')
                            ->label('แนะนำโดย')
                            ->placeholder('-')
                            ->url(fn ($record) => $record->referrer ? route('filament.admin.resources.users.view', $record->referrer) : null),
                        TextEntry::make('referrals_count')
                            ->label('จำนวนผู้ที่แนะนำมา')
                            ->state(fn ($record) => $record->referrals()->count())
                            ->suffix(' คน'),
                        TextEntry::make('referral_credited_at')
                            ->label('ได้รับเครดิตเมื่อ')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                    ])
                    ->columns(2),
                Section::make('ผู้ที่แนะนำมา')
                    ->schema([
                        RepeatableEntry::make('referrals')
                            ->label('')
                            ->schema([
                                TextEntry::make('name')->label('ชื่อ'),
                                TextEntry::make('email')->label('อีเมล'),
                                TextEntry::make('created_at')->label('สมัครเมื่อ')->dateTime('d/m/Y'),
                            ])
                            ->columns(3),
                    ])
                    ->visible(fn ($record): bool => $record !== null && $record->referrals()->exists())
                    ->collapsed(),
            ]);
    }
}
