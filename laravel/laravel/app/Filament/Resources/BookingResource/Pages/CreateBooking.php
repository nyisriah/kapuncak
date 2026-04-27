<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;
protected function mutateFormDataBeforeCreate(array $data): array
{
    \App\Filament\Resources\BookingResource::validateBookingDates($data);

    return $data;
}
    protected function handleRecordCreation(array $data): Model
    {
        BookingResource::validateBookingDates($data);

        return DB::transaction(function () use ($data): Model {
            $record = parent::handleRecordCreation($data);
            BookingResource::syncBookingDates($record);

            return $record;
        });
    }
}
