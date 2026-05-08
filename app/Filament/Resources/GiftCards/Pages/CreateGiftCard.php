<?php

namespace App\Filament\Resources\GiftCards\Pages;

use App\Filament\Resources\GiftCards\GiftCardResource;
use App\Models\GiftCard;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateGiftCard extends CreateRecord
{
    protected static string $resource = GiftCardResource::class;

    private string $issuedCode = '';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $plainCode = trim((string) ($data['plain_code'] ?? '')) ?: GiftCard::generatePlainCode();
        $this->issuedCode = $plainCode;

        unset($data['plain_code']);

        $data['code_hash'] = GiftCard::hashCode($plainCode);
        $data['code_last4'] = substr($plainCode, -4);
        $data['balance'] = $data['initial_balance'];
        $data['currency'] = 'THB';
        $data['issued_by'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        Notification::make()
            ->title('สร้างบัตรของขวัญเรียบร้อย')
            ->body("รหัสเต็ม: {$this->issuedCode}")
            ->success()
            ->persistent()
            ->send();
    }
}
