<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditBooking extends EditRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->disabled(fn (Model $record): bool => in_array($record->status, ['paid', 'completed'])),
        ];
    }

protected function mutateFormDataBeforeSave(array $data): array
{
    \App\Filament\Resources\BookingResource::validateBookingDates($data, $this->record);

    return $data;
}

    protected function canEdit(): bool
    {
        return $this->record?->status !== 'completed';
    }

    protected function canDelete(): bool
    {
        return ! in_array($this->record?->status, ['paid', 'completed']);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        BookingResource::validateBookingDates($data, $record);

        return DB::transaction(function () use ($record, $data): Model {
            $record = parent::handleRecordUpdate($record, $data);
            BookingResource::syncBookingDates($record);

            return $record;
        });
    }
}
