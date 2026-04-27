<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VillaResource\Pages;
use App\Filament\Resources\VillaResource\RelationManagers;
use App\Models\Villa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VillaResource extends Resource
{
    protected static ?string $model = Villa::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('contact_pengelola_villa')
                    ->label('Kontak Pengelola Villa')
                    ->tel()
                    ->maxLength(20),

                Forms\Components\TextInput::make('google_maps_link')
                    ->label('Link Google Maps')
                    ->url()
                    ->placeholder('https://maps.google.com/...'),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter villa name'),

                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Leave empty to auto-generate from the villa name.')
                    ->placeholder('villa-puncak-indah'),

                Forms\Components\Textarea::make('description')
                    ->nullable()
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->placeholder('Describe the villa'),

                Forms\Components\TextInput::make('location')
                    ->nullable()
                    ->maxLength(255)
                    ->placeholder('Enter location'),

                Forms\Components\TextInput::make('max_guests')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(999),

                Forms\Components\TextInput::make('bedrooms')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(999),

                Forms\Components\TextInput::make('weekday_price')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->prefix('Rp')
                    ->label('Weekday Price'),

                Forms\Components\TextInput::make('weekend_price')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->prefix('Rp')
                    ->label('Weekend Price'),

                Forms\Components\CheckboxList::make('facilities')
                    ->nullable()
                    ->columnSpanFull()
                    ->options([
                        'kolam private' => 'Kolam Private',
                        'kolam gabung' => 'Kolam Gabung',
                        'playground' => 'Playground',
                        'taman' => 'Taman',
                        'halaman rumput' => 'Halaman Rumput',
                        'parkir mobil' => 'Parkir Mobil',
                        'parkir motor' => 'Parkir Motor',
                        'wifi' => 'WiFi',
                        'tv' => 'TV',
                        'ac' => 'AC',
                        'kipas angin' => 'Kipas Angin',
                        'sound system' => 'Sound System',
                        'billiard' => 'Billiard',
                        'tenis meja' => 'Tenis Meja',
                        'free extrabed' => 'Free Extrabed',
                        'alat BBQ' => 'Alat BBQ',
                        'roofthop' => 'Roofthop',
                        'rice cooker' => 'Rice Cooker',
                        'water heater' => 'Water Heater',
                        'akses untuk disabilitas' => 'Akses Untuk Disabilitas',
                        'free air galon' => 'Free Air Galon',
                        'kitcen set lengkap' => 'Kitchen Set Lengkap',
                        'aula' => 'Aula',
                    ])
                    ->columns(2),
                    
                Forms\Components\Textarea::make('rules')
                    ->nullable()
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->placeholder('Enter house rules'),

                Forms\Components\FileUpload::make('images')
                    ->multiple()
                    ->image()
                    ->nullable()
                    ->columnSpanFull()
                    ->maxFiles(10)
                    ->maxSize(5120)
                    ->directory('villas'),

                Forms\Components\Select::make('status')
                    ->required()
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->default('active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('location')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('weekday_price')
                    ->money('IDR')
                    ->sortable()
                    ->label('Weekday Price'),

                Tables\Columns\TextColumn::make('weekend_price')
                    ->money('IDR')
                    ->sortable()
                    ->label('Weekend Price'),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListVillas::route('/'),
            'create' => Pages\CreateVilla::route('/create'),
            'edit' => Pages\EditVilla::route('/{record}/edit'),
        ];
    }
}
