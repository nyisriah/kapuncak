<?php

namespace App\Filament\Resources\BookingResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class BookingDatesRelationManager extends RelationManager
{
    protected static string $relationship = 'bookingDates';

    protected static ?string $recordTitleAttribute = 'date';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Booked Date')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('date')
            ->filters([])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
