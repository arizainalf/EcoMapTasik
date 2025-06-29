<?php
namespace App\Filament\Resources;

use App\Filament\Resources\SerapanSampahResource\Pages;
use App\Models\SerapanSampah;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SerapanSampahResource extends Resource
{
    protected static ?string $model = SerapanSampah::class;

    protected static ?string $navigationIcon = 'heroicon-o-trash';

    protected static ?string $navigationLabel = 'Sampah';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('tanggal')
                                    ->label('Tanggal')
                                    ->required()
                                    ->date()
                                    ->default(now()),
                                Forms\Components\Select::make('tempat_id') // gunakan tempat_id agar relasi lebih jelas
                                    ->label('Tempat')
                                    ->relationship('tempat', 'tempat')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state) {
                                            $tempat = \App\Models\Tempat::find($state);
                                            if ($tempat) {
                                                $set('kk', $tempat->kk);
                                            }
                                        } else {
                                            $set('kk', null);
                                        }
                                    })
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('tempat')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('kk')
                                            ->label('KK')
                                            ->numeric()
                                            ->required(),
                                    ]),

                                Forms\Components\TextInput::make('kk')
                                    ->label('Jumlah KK')
                                    ->required()
                                    ->numeric(),

                                Forms\Components\TextInput::make('organic')
                                    ->label('Organik (kg)')
                                    ->required()
                                    ->numeric()
                                    ->reactive()
                                    ->afterStateUpdated(fn($state, callable $set, callable $get) => self::updateTotal($set, $get)),

                                Forms\Components\TextInput::make('anorganic')
                                    ->label('Anorganik (kg)')
                                    ->required()
                                    ->numeric()
                                    ->reactive()
                                    ->afterStateUpdated(fn($state, callable $set, callable $get) => self::updateTotal($set, $get)),

                                Forms\Components\TextInput::make('residu')
                                    ->label('Residu (kg)')
                                    ->required()
                                    ->numeric()
                                    ->reactive()
                                    ->afterStateUpdated(fn($state, callable $set, callable $get) => self::updateTotal($set, $get)),

                                Forms\Components\TextInput::make('total')
                                    ->label('Total Sampah (kg)')
                                    ->numeric()
                                    ->disabled()    // otomatis, tidak bisa diubah manual
                                    ->dehydrated(), // tetap ikut tersubmit
                            ]),
                    ]),
            ]);
    }

    protected static function updateTotal(callable $set, callable $get): void
    {
        $organic   = floatval($get('organic') ?? 0);
        $anorganic = floatval($get('anorganic') ?? 0);
        $residu    = floatval($get('residu') ?? 0);

        $set('total', $organic + $anorganic + $residu);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tempat.tempat')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tempat.kk')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('organic')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('anorganic')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('residu')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
            'index'  => Pages\ListSerapanSampahs::route('/'),
            'create' => Pages\CreateSerapanSampah::route('/create'),
            'edit'   => Pages\EditSerapanSampah::route('/{record}/edit'),
            'report' => Pages\SerapanSampahReport::route('/report'),
        ];
    }
}
