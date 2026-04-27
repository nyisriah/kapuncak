<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Filament\Resources\ReviewResource\RelationManagers;
use App\Models\Review;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->required()
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->label('User')
                    ->live(),

                Forms\Components\Select::make('villa_id')
                    ->required()
                    ->relationship('villa', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Villa'),

                Forms\Components\Select::make('booking_id')
                    ->required()
                    ->relationship(
                        'booking',
                        'id',
                        fn (Builder $query, Forms\Get $get) => $query
                            ->where('user_id', $get('user_id'))
                            ->where('status', 'completed')
                    )
                    ->searchable()
                    ->preload()
                    ->label('Booking')
                    ->placeholder('Pilih booking yang sudah completed')
                    ->formatStateUsing(fn ($state, Booking $record = null) => 
                        $record ? "Booking #{$record->id} - {$record->villa->name}" : $state
                    )
                    ->getOptionLabelsUsing(fn (array $values) => 
                        Booking::whereIn('id', $values)
                            ->get()
                            ->mapWithKeys(fn ($booking) => [
                                $booking->id => "Booking #{$booking->id} - {$booking->villa->name}"
                            ])
                            ->toArray()
                    ),

                Forms\Components\Select::make('rating')
                    ->required()
                    ->options([
                        1 => '⭐ 1 - Sangat Buruk',
                        2 => '⭐⭐ 2 - Buruk',
                        3 => '⭐⭐⭐ 3 - Cukup',
                        4 => '⭐⭐⭐⭐ 4 - Baik',
                        5 => '⭐⭐⭐⭐⭐ 5 - Sangat Baik',
                    ])
                    ->label('Rating'),

                Forms\Components\Textarea::make('comment')
                    ->nullable()
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->placeholder('Tuliskan komentar Anda (opsional)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label('User'),

                Tables\Columns\TextColumn::make('villa.name')
                    ->searchable()
                    ->sortable()
                    ->label('Villa'),

                Tables\Columns\TextColumn::make('booking_id')
                    ->label('Booking ID')
                    ->formatStateUsing(fn ($state) => "#{$state}"),

                Tables\Columns\BadgeColumn::make('rating')
                    ->colors([
                        'danger' => [1, 2],
                        'warning' => 3,
                        'success' => [4, 5],
                    ])
                    ->formatStateUsing(fn ($state) => "{$state} ⭐")
                    ->sortable(),

                Tables\Columns\TextColumn::make('comment')
                    ->limit(50)
                    ->tooltip(fn (Tables\Columns\TextColumn $column): ?string => $column->getState())
                    ->label('Komentar'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->label('Tanggal Review'),
            ])
            ->filters([
                SelectFilter::make('rating')
                    ->options([
                        1 => '⭐ 1 - Sangat Buruk',
                        2 => '⭐⭐ 2 - Buruk',
                        3 => '⭐⭐⭐ 3 - Cukup',
                        4 => '⭐⭐⭐⭐ 4 - Baik',
                        5 => '⭐⭐⭐⭐⭐ 5 - Sangat Baik',
                    ])
                    ->label('Rating'),

                SelectFilter::make('villa')
                    ->relationship('villa', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Villa'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}