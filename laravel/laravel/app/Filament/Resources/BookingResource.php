<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Filament\Resources\BookingResource\RelationManagers\BookingDatesRelationManager;
use App\Models\Booking;
use App\Models\BookingDate;
use App\Models\Villa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Filament\Tables\Columns\ImageColumn;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationGroup = 'Bookings';

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Card::make()
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->label('Customer'),

                    Forms\Components\Select::make('villa_id')
                        ->relationship('villa', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set, callable $get) => static::maybePopulateTotalPrice($set, $get))
                        ->label('Villa'),

                    Forms\Components\DatePicker::make('checkin_date')
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set, callable $get) => static::maybePopulateTotalPrice($set, $get))
                        ->label('Check-In Date'),

                    Forms\Components\DatePicker::make('checkout_date')
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set, callable $get) => static::maybePopulateTotalPrice($set, $get))
                        ->label('Check-Out Date'),

                    Forms\Components\Select::make('status')
                        ->required()
                        ->options(self::getStatusOptions())
                        ->default('pending')
                        ->label('Status'),

                    Forms\Components\TextInput::make('total_price')
                        ->required()
                        ->numeric()
                        ->minValue(0.01)
                        ->step(0.01)
                        ->prefix('Rp ')
                        ->label('Total Price'),

                    Forms\Components\TextInput::make('markup_amount')
                        ->numeric()
                        ->nullable()
                        ->minValue(0)
                        ->step(0.01)
                        ->prefix('Rp ')
                        ->label('Markup Amount'),

                    Forms\Components\Select::make('affiliate_user_id')
                        ->relationship('affiliateUser', 'name')
                        ->searchable()
                        ->preload()
                        ->placeholder('No affiliate')
                        ->label('Affiliate'),

                    Forms\Components\TextInput::make('user_ip')
                        ->disabled()
                        ->default(fn () => request()->ip())
                        ->label('User IP'),
                ])

        ]);
}
            
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
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
ImageColumn::make('payment.proof')
    ->label('Bukti Bayar')
    ->disk('public')
    ->height(80)
    ->square()
    ->extraImgAttributes([
        'style' => 'cursor:pointer',
        'onclick' => "window.open(this.src, '_blank')"
    ]),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (?string $state): ?string => $state ? ucfirst(str_replace('_', ' ', $state)) : null)
                    ->colors(self::getStatusBadgeColors())
                    ->sortable(),

                Tables\Columns\TextColumn::make('nights')
                    ->label('Duration')
                    ->getStateUsing(fn (Booking $record): string => $record->getNights() . ' nights'),

                Tables\Columns\TextColumn::make('approved_at')
                    ->label('Approved At')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('payment_deadline')
                    ->label('Payment Deadline')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->color(fn (Booking $record): string => $record->isExpired() ? 'danger' : 'default')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(self::getStatusOptions())
                    ->label('Status'),
            ])
            ->actions([
                EditAction::make()
                    ->disabled(fn (Booking $record): bool => in_array($record->status, ['confirmed', 'rejected', 'paid']))
                    ->before(function (EditAction $action, Booking $record) {
                        // 🔐 BACKEND VALIDATION: Cek status transition valid
                        $newStatus = data_get($action->getRecord(), 'status');
                        if ($newStatus && !$record->canTransitionTo($newStatus)) {
                            throw new \Illuminate\Validation\ValidationException(
                                \Illuminate\Support\Facades\Validator::make([], [])->errors()
                            );
                        }
                    }),

                DeleteAction::make()
                    ->disabled(fn (Booking $record): bool => in_array($record->status, ['paid', 'confirmed', 'rejected'])),

                Action::make('approve')
                    ->label('Approve')
                    ->action(function (Booking $record) {
                        // 🔐 SECURITY: Validate status transition
                        if (!$record->canTransitionTo('approved')) {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('Error')
                                ->body('Status transition tidak valid')
                                ->send();
                            return;
                        }
                        
                        // ✅ Use enhanced approve method with deadline
                        if ($record->approve(24)) { // 24 hours payment deadline
                            \Filament\Notifications\Notification::make()
                                ->success()
                                ->title('Booking Approved')
                                ->body('Payment deadline set to 24 hours from now')
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('Error')
                                ->body('Failed to approve booking')
                                ->send();
                        }
                    })
                    ->requiresConfirmation()
                    ->visible(fn (Booking $record): bool => $record->status === 'pending')
                    ->color('primary')
                    ->icon('heroicon-s-check'),

                Action::make('reject')
                    ->label('Reject')
                    ->action(function (Booking $record) {
                        // 🔐 SECURITY: Validate status transition
                        if (!$record->canTransitionTo('rejected')) {
                            return;
                        }
                        $record->update(['status' => 'rejected']);
                    })
                    ->requiresConfirmation()
                    ->visible(fn (Booking $record): bool => in_array($record->status, ['pending', 'approved']))
                    ->color('danger')
                    ->icon('heroicon-s-x-mark'),

                Action::make('confirm')
                    ->label('Confirm Payment')
                    ->action(function (Booking $record) {
                        // 🔐 SECURITY: Validate status transition & payment exists
                        if (!$record->canTransitionTo('confirmed')) {
                            return;
                        }
                        if (!$record->payment()->exists()) {
                            return;
                        }
                        $record->update(['status' => 'confirmed']);
                    })
                    ->requiresConfirmation()
                    ->visible(fn (Booking $record): bool => $record->status === 'paid' && $record->payment()->exists())
                    ->color('success')
                    ->icon('heroicon-o-home'),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            BookingDatesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'paid' => 'Paid',
            'confirmed' => 'Confirmed',
            'cancelled' => 'Cancelled',
        ];
    }

    public static function getStatusBadgeColors(): array
    {
        return [
            'pending' => 'secondary',
            'approved' => 'primary',
            'rejected' => 'danger',
            'paid' => 'success',
            'confirmed' => 'success',
            'cancelled' => 'warning',
        ];
    }

    public static function validateBookingDates(array $data, ?Booking $record = null): void
    {
        if (empty($data['villa_id']) || empty($data['checkin_date']) || empty($data['checkout_date'])) {
            return;
        }

        $checkin = Carbon::parse($data['checkin_date']);
        $checkout = Carbon::parse($data['checkout_date']);

        if (! $checkin->lt($checkout)) {
            throw ValidationException::withMessages([
                'checkout_date' => 'Check-out date must be later than check-in date.',
            ]);
        }

        $lastDate = $checkout->copy()->subDay();

        if (BookingDate::where('villa_id', $data['villa_id'])
            ->when($record?->id, fn ($query, $id) => $query->where('booking_id', '<>', $id))
            ->whereBetween('date', [$checkin->toDateString(), $lastDate->toDateString()])
            ->exists()) {
            throw ValidationException::withMessages([
                'checkin_date' => 'The selected villa is already booked for one or more of the chosen dates.',
            ]);
        }
    }

    public static function syncBookingDates(Booking $booking): void
    {
        if (! $booking->villa_id || ! $booking->checkin_date || ! $booking->checkout_date) {
            return;
        }

        $booking->bookingDates()->delete();

        $checkin = $booking->checkin_date->copy();
        $checkout = $booking->checkout_date->copy();

        if (! $checkin->lt($checkout)) {
            return;
        }

        $dates = [];
        for ($date = $checkin; $date->lt($checkout); $date->addDay()) {
            $dates[] = [
                'booking_id' => $booking->id,
                'villa_id' => $booking->villa_id,
                'date' => $date->toDateString(),
            ];
        }

        if (! empty($dates)) {
            BookingDate::insert($dates);
        }
    }

    public static function calculateTotalPrice(?int $villaId, ?string $checkinDate, ?string $checkoutDate): ?float
    {
        if (! $villaId || ! $checkinDate || ! $checkoutDate) {
            return null;
        }

        $checkin = Carbon::parse($checkinDate);
        $checkout = Carbon::parse($checkoutDate);

        if (! $checkin->lt($checkout)) {
            return null;
        }

        $villa = Villa::find($villaId);
        if (! $villa) {
            return null;
        }

        $total = 0;
        for ($date = $checkin->copy(); $date->lt($checkout); $date->addDay()) {
            $total += $villa->getPriceForDate($date->toDateString());
        }

        return $total;
    }

    private static function maybePopulateTotalPrice(callable $set, callable $get): void
    {
        if ($get('total_price')) {
            return;
        }

        $computedPrice = self::calculateTotalPrice($get('villa_id'), $get('checkin_date'), $get('checkout_date'));
        if ($computedPrice !== null) {
            $set('total_price', $computedPrice);
        }
    }
}
