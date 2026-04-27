<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AffiliateCommissionResource\Pages;
use App\Models\AffiliateCommission;
use App\Models\Booking;
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
use Illuminate\Validation\Rule;

class AffiliateCommissionResource extends Resource
{
    protected static ?string $model = AffiliateCommission::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Affiliate';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('affiliate_user_id')
                            ->relationship('affiliateUser', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Affiliate User'),

                        Forms\Components\Select::make('booking_id')
                            ->required()
                            ->relationship('booking', 'id')
                            ->searchable()
                            ->preload()
                            ->getOptionLabelsUsing(fn (array $values) =>
                                Booking::whereIn('id', $values)
                                    ->with('villa')
                                    ->get()
                                    ->mapWithKeys(fn (Booking $booking) => [
                                        $booking->id => sprintf(
                                            'Booking #%s - %s',
                                            $booking->id,
                                            $booking->villa?->name ?? 'No Villa'
                                        ),
                                    ])
                                    ->toArray()
                            )
                            ->rules([
                                'required',
                                Rule::exists('bookings', 'id')->where(fn ($query) => $query->where('status', 'completed')),
                            ])
                            ->unique(ignoreRecord: true)
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set, callable $get) => static::maybePopulateCommissionAmount($set, $get))
                            ->label('Booking'),

                        Forms\Components\TextInput::make('commission_amount')
                            ->required()
                            ->numeric()
                            ->minValue(0.01)
                            ->step(0.01)
                            ->prefix('Rp ')
                            ->label('Commission Amount'),

                        Forms\Components\Select::make('status')
                            ->required()
                            ->options(self::getStatusOptions())
                            ->default('pending')
                            ->label('Status'),

                        Forms\Components\DateTimePicker::make('eligible_at')
                            ->nullable()
                            ->label('Eligible At')
                            ->default(fn () => Carbon::now()->addDays(7)),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('affiliateUser.name')
                    ->label('Affiliate')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('booking.id')
                    ->label('Booking')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('booking.villa.name')
                    ->label('Villa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('commission_amount')
                    ->label('Amount')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (?string $state): ?string => $state ? ucfirst(str_replace('_', ' ', $state)) : null)
                    ->colors(self::getStatusBadgeColors())
                    ->sortable(),

                Tables\Columns\TextColumn::make('eligible_at')
                    ->label('Eligible At')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

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
                    ->disabled(fn (AffiliateCommission $record): bool => $record->isPaid()),

                DeleteAction::make()
                    ->disabled(fn (AffiliateCommission $record): bool => in_array($record->status, ['approved', 'paid'])),

                Action::make('approve')
                    ->label('Approve Commission')
                    ->action(fn (AffiliateCommission $record) => $record->markAsApproved())
                    ->requiresConfirmation()
                    ->visible(fn (AffiliateCommission $record): bool => $record->status === 'pending')
                    ->color('primary')
                    ->icon('heroicon-s-check'),

                Action::make('mark_as_paid')
                    ->label('Mark as Paid')
                    ->action(fn (AffiliateCommission $record) => $record->markAsPaid())
                    ->requiresConfirmation()
                    ->visible(fn (AffiliateCommission $record): bool => $record->status === 'approved')
                    ->color('success')
                    ->icon('heroicon-s-currency-dollar'),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAffiliateCommissions::route('/'),
            'create' => Pages\CreateAffiliateCommission::route('/create'),
            'edit' => Pages\EditAffiliateCommission::route('/{record}/edit'),
        ];
    }

    public static function getStatusOptions(): array
    {
        return [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'paid' => 'Paid',
        ];
    }

    public static function getStatusBadgeColors(): array
    {
        return [
            'pending' => 'warning',
            'approved' => 'primary',
            'paid' => 'success',
        ];
    }

    public static function calculateCommissionAmount(?int $bookingId): ?float
    {
        if (! $bookingId) {
            return null;
        }

        $booking = Booking::with('villa')->find($bookingId);
        if (! $booking) {
            return null;
        }

        if ($booking->markup_amount > 0) {
            return (float) $booking->markup_amount;
        }

        if ($booking->total_price > 0) {
            return (float) round($booking->total_price * 0.1, 2);
        }

        return null;
    }

    private static function maybePopulateCommissionAmount(callable $set, callable $get): void
    {
        if ($get('commission_amount')) {
            return;
        }

        $computedAmount = self::calculateCommissionAmount($get('booking_id'));
        if ($computedAmount !== null) {
            $set('commission_amount', $computedAmount);
        }
    }
}
