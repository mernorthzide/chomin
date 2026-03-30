<?php

namespace App\Filament\Pages;

use App\Models\ShippingSetting;
use App\Models\SiteSetting;
use BackedEnum;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|\UnitEnum|null $navigationGroup = 'เนื้อหา';

    protected static ?string $navigationLabel = 'ตั้งค่าเว็บไซต์';

    protected static ?string $title = 'ตั้งค่าเว็บไซต์';

    protected string $view = 'filament.pages.site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $shipping = ShippingSetting::current();

        $this->form->fill([
            'site_name' => SiteSetting::get('site_name', 'Chomin'),
            'site_description' => SiteSetting::get('site_description'),
            'site_phone' => SiteSetting::get('site_phone'),
            'site_email' => SiteSetting::get('site_email'),
            'promptpay_number' => SiteSetting::get('promptpay_number'),
            'promptpay_name' => SiteSetting::get('promptpay_name'),
            'shipping_fee' => $shipping->shipping_fee,
            'free_shipping_min_amount' => $shipping->free_shipping_min_amount,
            'points_per_baht' => SiteSetting::get('points_per_baht', '1'),
            'baht_per_point' => SiteSetting::get('baht_per_point', '1'),
            'footer_text' => SiteSetting::get('footer_text'),
            'announcement_text' => SiteSetting::get('announcement_text'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('ข้อมูลร้าน')
                    ->schema([
                        TextInput::make('site_name')
                            ->label('ชื่อร้าน')
                            ->required(),
                        TextInput::make('site_phone')
                            ->label('เบอร์โทร'),
                        TextInput::make('site_email')
                            ->label('อีเมล')
                            ->email(),
                        Textarea::make('site_description')
                            ->label('คำอธิบายร้าน')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('PromptPay')
                    ->schema([
                        TextInput::make('promptpay_number')
                            ->label('หมายเลข PromptPay'),
                        TextInput::make('promptpay_name')
                            ->label('ชื่อบัญชี'),
                    ])
                    ->columns(2),
                Section::make('ค่าจัดส่ง')
                    ->schema([
                        TextInput::make('shipping_fee')
                            ->label('ค่าจัดส่งมาตรฐาน (บาท)')
                            ->numeric()
                            ->prefix('฿'),
                        TextInput::make('free_shipping_min_amount')
                            ->label('ยอดขั้นต่ำสำหรับจัดส่งฟรี (บาท)')
                            ->numeric()
                            ->prefix('฿')
                            ->nullable(),
                    ])
                    ->columns(2),
                Section::make('แต้มสะสม')
                    ->schema([
                        TextInput::make('points_per_baht')
                            ->label('แต้มต่อ 1 บาท')
                            ->numeric()
                            ->default(1),
                        TextInput::make('baht_per_point')
                            ->label('มูลค่าต่อ 1 แต้ม (บาท)')
                            ->numeric()
                            ->default(1),
                    ])
                    ->columns(2),
                Section::make('เนื้อหา')
                    ->schema([
                        Textarea::make('announcement_text')
                            ->label('ข้อความประกาศ (แถบด้านบน)')
                            ->columnSpanFull(),
                        Textarea::make('footer_text')
                            ->label('ข้อความ Footer')
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Save site settings
        $siteKeys = ['site_name', 'site_description', 'site_phone', 'site_email',
            'promptpay_number', 'promptpay_name', 'points_per_baht', 'baht_per_point',
            'footer_text', 'announcement_text'];

        foreach ($siteKeys as $key) {
            SiteSetting::set($key, $data[$key] ?? null);
        }

        // Save shipping settings
        $shipping = ShippingSetting::current();
        $shipping->update([
            'shipping_fee' => $data['shipping_fee'],
            'free_shipping_min_amount' => $data['free_shipping_min_amount'] ?: null,
        ]);

        Notification::make()
            ->title('บันทึกการตั้งค่าเรียบร้อยแล้ว')
            ->success()
            ->send();
    }
}
