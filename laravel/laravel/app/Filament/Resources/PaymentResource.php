<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Select::make('booking_id')
                    ->required()
                    ->relationship(
                        'booking',
                        'id',
                        fn (Builder $query) => $query->with('user', 'villa')
                    )
                    ->searchable()
                    ->preload()
                    ->getOptionLabelsUsing(fn (array $values) => 
                        Booking::whereIn('id', $values)
                            ->with('user', 'villa')
                            ->get()
                            ->mapWithKeys(fn ($booking) => [
                                $booking->id => "Booking #{$booking->id} - {$booking->user->name} ({$booking->villa->name})"
                            ])
                            ->toArray()
                    )
                    ->label('Booking')
                    ->disabled(fn (string $operation) => $operation === 'edit'),

                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->minValue(0.01)
                    ->step(0.01)
                    ->prefix('Rp ')
                    ->label('Amount')
                    ->disabled(fn (string $operation) => $operation === 'edit'),

                // ✅ MANUAL PAYMENT
                Forms\Components\Select::make('payment_method')
                    ->required()
                    ->options([
                        'bank_transfer' => 'Bank Transfer',
                        'e_wallet' => 'E-Wallet',
                    ])
                    ->default('bank_transfer')
                    ->label('Payment Method')
                    ->disabled(fn (string $operation) => $operation === 'edit'),

                // ✅ OPTIONAL (TIDAK WAJIB LAGI)
                Forms\Components\TextInput::make('doku_transaction_id')
                    ->nullable()
                    ->maxLength(255)
                    ->label('Transaction ID (Optional)')
                    ->disabled(fn (string $operation) => $operation === 'edit'),

                Forms\Components\Select::make('status')
                    ->required()
                    ->options([
                        'pending' => 'Pending',
                        'success' => 'Success',
                        'rejected' => 'Rejected',
                        'failed' => 'Failed',
                    ])
                    ->default('pending')
                    ->label('Status'),

                Forms\Components\Textarea::make('rejection_reason')
                    ->label('Rejection Reason')
                    ->placeholder('Explain why this payment was rejected...')
                    ->visible(fn (callable $get) => $get('status') === 'rejected')
                    ->rows(3),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('booking_id')
                    ->formatStateUsing(fn ($state) => "#{$state}")
                    ->label('Booking ID'),
                Tables\Columns\ImageColumn::make('proof')
                    ->disk('public')
                    ->label('Bukti Bayar'),
                Tables\Columns\TextColumn::make('booking.user.name')
                    ->label('Customer'),

                Tables\Columns\TextColumn::make('booking.villa.name')
                    ->label('Villa'),

                Tables\Columns\TextColumn::make('amount')
                    ->money('IDR')
                    ->label('Amount'),

                Tables\Columns\TextColumn::make('payment_method')
                    ->formatStateUsing(fn ($state) => ucfirst(str_replace('_', ' ', $state)))
                    ->label('Method'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'Pending Verification',
                        'success' => 'Payment Confirmed',
                        'rejected' => 'Payment Rejected',
                        'failed' => 'Payment Failed',
                        default => ucfirst($state),
                    })
                    ->color(fn ($state) => match ($state) {
                        'pending' => 'warning',
                        'success' => 'success',
                        'rejected' => 'danger',
                        'failed' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('rejection_reason')
                    ->label('Rejection Reason')
                    ->limit(50)
                    ->tooltip(fn (Payment $record): string => $record->rejection_reason ?? '')
                    ->visible(fn (Payment $record) => $record->rejection_reason)
                    ->color('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->label('Created At'),

            ])
            ->filters([

                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'success' => 'Success',
                        'rejected' => 'Rejected',
                        'failed' => 'Failed',
                    ]),

                SelectFilter::make('payment_method')
                    ->options([
                        'bank_transfer' => 'Bank Transfer',
                        'e_wallet' => 'E-Wallet',
                    ]),

            ])
            ->actions([

                // ✅ APPROVE PAYMENT
                Tables\Actions\Action::make('approve')
                    ->label('Approve Payment')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->visible(fn (Payment $record) => $record->status === 'pending' && $record->booking->status === 'paid')
                    ->requiresConfirmation()
                    ->action(function (Payment $record) {
                        // ✅ Use enhanced approve method
                        if ($record->approve()) {
                            \Filament\Notifications\Notification::make()
                                ->success()
                                ->title('Payment Approved')
                                ->body('Payment has been confirmed and booking is now confirmed.')
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('Error')
                                ->body('Failed to approve payment')
                                ->send();
                        }
                    }),

                // ✅ REJECT PAYMENT WITH REASON
                Tables\Actions\Action::make('reject')
                    ->label('Reject Payment')
                    ->color('danger')
                    ->icon('heroicon-o-x-mark')
                    ->visible(fn (Payment $record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->required()
                            ->placeholder('Please explain why this payment is being rejected...')
                            ->rows(4),
                    ])
                    ->action(function (Payment $record, array $data) {
                        // ✅ Use enhanced reject method with reason
                        if ($record->reject($data['rejection_reason'])) {
                            \Filament\Notifications\Notification::make()
                                ->success()
                                ->title('Payment Rejected')
                                ->body('Payment has been rejected. User can re-upload payment.')
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('Error')
                                ->body('Failed to reject payment')
                                ->send();
                        }
                    }),

                Tables\Actions\EditAction::make()
                    ->disabled(fn (Payment $record) => !in_array($record->status, ['pending', 'rejected'])),

                Tables\Actions\DeleteAction::make()
                    ->disabled(fn (Payment $record) => $record->status !== 'pending'),

            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}