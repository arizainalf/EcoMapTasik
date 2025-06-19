<?php
namespace App\Filament\Resources;

use App\Filament\Resources\LocationResource\Pages;
use App\Models\Location;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LocationResource extends Resource
{
    protected static ?string $model           = Location::class;
    protected static ?string $navigationIcon  = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'Lokasi';
    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make()
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->label('Nama Lokasi'),

                    Textarea::make('address')
                        ->label('Alamat'),

                    Textarea::make('description')
                        ->label('Deskripsi'),
                    FileUpload::make('image')
                        ->image()
                        ->label('Gambar'),

                    TextInput::make('latitude')
                        ->label('Latitude')
                        ->required()
                        ->numeric()
                        ->step('any')
                        ->extraAttributes(['id' => 'latitude-input'])
                        ->live(),

                    TextInput::make('longitude')
                        ->label('Longitude')
                        ->required()
                        ->numeric()
                        ->step('any')
                        ->extraAttributes(['id' => 'longitude-input'])
                        ->live(),
                    View::make('components.peta-picker')
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Lokasi')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Alamat')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar')
                    ->width(100)
                    ->height(100),
                Tables\Columns\TextColumn::make('latitude')
                    ->sortable()
                    ->label('Latitude')
                    ->formatStateUsing(fn($state) => number_format($state, 6)),
                Tables\Columns\TextColumn::make('longitude')
                    ->label('Lng')
                    ->label('Latitude')
                    ->formatStateUsing(fn($state) => number_format($state, 6)),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLocations::route('/'),
            'create' => Pages\CreateLocation::route('/create'),
            'edit'   => Pages\EditLocation::route('/{record}/edit'),
        ];
    }
}
