<?php
namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model           = Product::class;
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Produk';
    protected static ?string $navigationIcon  = 'heroicon-o-archive-box';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make([
                    Forms\Components\Select::make('category_id')
                        ->options(function (): array {
                            return Category::all()->pluck('name', 'id')->all();
                        })
                        ->label('Kategori')
                        ->columnSpan(1)
                        ->required(),

                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->columnSpan(1)
                        ->label('Nama Produk')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('stock')
                        ->required()
                        ->columnSpan(1)
                        ->label('Stok')
                        ->numeric()
                        ->default(0),

                    Forms\Components\Textarea::make('description')
                        ->label('Deskripsi Produk')
                        ->columnSpan(3),

                    Forms\Components\TextInput::make('price')
                        ->required()
                        ->label('Harga Produk')
                        ->numeric()
                        ->prefix('$'),

                    Forms\Components\TextInput::make('weight')
                        ->required()
                        ->columnSpan(1)
                        ->label('Berat Produk')
                        ->numeric()
                        ->default(0.00),

                    Forms\Components\FileUpload::make('image')
                        ->label('Gambar Produk')
                        ->image()
                        ->directory('product')
                        ->visibility('public')
                        ->uploadingMessage('Mengunggah...')
                        ->imageResizeMode('cover')
                        ->imageCropAspectRatio('1:1')
                        ->imageResizeTargetWidth('1080')
                        ->imageResizeTargetHeight('1080'),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->searchable()
                    ->label('Kategori')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('idr')
                    ->label('Harga')
                    ->sortable(),
                Tables\Columns\TextColumn::make('weight')
                    ->label('Berat (Kg)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar')
                    ->width(100)
                    ->height(100),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'view'  => Pages\ViewProduct::route('/{record}'),
            'edit'  => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
