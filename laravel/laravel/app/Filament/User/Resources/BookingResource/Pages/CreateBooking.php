<?php

namespace App\Filament\User\Resources\BookingResource\Pages;

use App\Filament\User\Resources\BookingResource;
use App\Models\BookingDate;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        // Set user_id to current authenticated user
        $data['user_id'] = auth()->id();

        return DB::transaction(function () use ($data): Model {
            $record = parent::handleRecordCreation($data);

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