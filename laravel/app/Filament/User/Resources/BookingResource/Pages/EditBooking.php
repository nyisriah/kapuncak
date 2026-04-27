<?php

namespace App\Filament\User\Resources\BookingResource\Pages;

use App\Filament\User\Resources\BookingResource;
use App\Models\BookingDate;
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
                ->disabled(fn (): bool => in_array($this->record->status, ['paid', 'completed'])),
        ];
    }

    protected function canEdit(): bool
    {
        return !in_array($this->record->status, ['paid', 'completed']);
    }

    protected function canDelete(): bool
    {
        return !in_array($this->record->status, ['paid', 'completed']);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data): Model {
            $record = parent::handleRecordUpdate($record, $data);

            // Sync booking dates
            if ($record->villa_id && $record->checkin_date && $record->checkout_date) {
                $record->bookingDates()->delete();

                $checkin = $record->checkin_date->copy();
                $checkout = $record->checkout_date->copy();

                if ($checkin->lt($checkout)) {
                    $dates = [];
                    for ($date = $checkin; $date->lt($checkout); $date->addDay()) {
                        $dates[] = [
                            'booking_id' => $record->id,
                            'villa_id' => $record->villa_id,
                            'date' => $date->toDateString(),
                        ];
                    }

                    if (!empty($dates)) {
                        BookingDate::insert($dates);
                    }
                }
            }

            return $record;
        });
    }
}