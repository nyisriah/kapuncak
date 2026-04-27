<?php

namespace App\Filament\User\Resources\BookingResource\Pages;

use App\Filament\User\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBooking extends ViewRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 🔥 TOMBOL BAYAR SEKARANG - hanya tampil jika booking status approved
            Actions\Action::make('pay_now')
                ->label('Bayar Sekarang')
                ->url(fn () => route('payments.create', $this->record->id))
                ->openUrlInNewTab()
                ->visible(fn (): bool => $this->record->status === 'approved')
                ->color('success')
                ->icon('heroicon-o-credit-card'),

            Actions\EditAction::make()
                ->disabled(fn (): bool => in_array($this->record->status, ['paid', 'confirmed'])),
        ];
    }
}