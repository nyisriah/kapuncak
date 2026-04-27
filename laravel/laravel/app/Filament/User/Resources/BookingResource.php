<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\BookingResource\Pages;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'My Bookings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('villa_id')
                            ->relationship('villa', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Villa'),

                        Forms\Components\DatePicker::make('checkin_date')
                            ->required()
                            ->rules(['required', 'date', 'before:checkout_date'])
                            ->label('Check-In Date'),

                        Forms\Components\DatePicker::make('checkout_date')
                            ->required()
                            ->rules(['required', 'date', 'after:checkin_date'])
                            ->label('Check-Out Date'),

                        Forms\Components\TextInput::make('total_price')
                            ->required()
                            ->numeric()
                            ->minValue(0.01)
                            ->step(0.01)
                            ->prefix('Rp ')
                            ->label('Total Price'),

                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'paid' => 'Paid',
                                'confirmed' => 'Confirmed',
                            ])
                            ->required()
                            ->default('pending')
                            ->disabled() // User cannot change status
                            ->label('Status'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Booking ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('villa.name')
                    ->label('Villa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('checkin_date')
                    ->label('Check-In')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('checkout_date')
                    ->label('Check-Out')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total Price')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (?string $state): ?string => $state ? ucfirst(str_replace('_', ' ', $state)) : null)
                    ->colors([
                        'secondary' => 'pending',
                        'primary' => 'approved',
                        'danger' => 'rejected',
                        'warning' => 'paid',
                        'success' => 'confirmed',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'paid' => 'Paid',
                        'confirmed' => 'Confirmed',
                    ])
                    ->label('Status'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->disabled(fn (Booking $record): bool => in_array($record->status, ['paid', 'completed'])),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'view' => Pages\ViewBooking::route('/{record}'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }
}