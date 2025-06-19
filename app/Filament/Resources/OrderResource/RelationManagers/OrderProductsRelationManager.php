<?php
namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class OrderProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'orderProducts';

    protected static ?string $title = 'Detail Produk';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('no')
                    ->label('No')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Produk'),
                Tables\Columns\ImageColumn::make('product.image')
                    ->label('Gambar')
                    ->height(100)
                    ->action(
                        Tables\Actions\Action::make('viewImage')
                            ->modalContent(fn($record) => view('tables.columns.payment-proof-modal', [
                                'image' => $record->product->image,
                            ]))
                            ->modalHeading('Gambar Produk')
                            ->modalSubmitAction(false) // Hilangkan tombol Kirim
                            ->modalCancelAction(false)
                    ),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Jumlah'),
                Tables\Columns\TextColumn::make('total_price')
                    ->money('IDR', locale: 'id')
                    ->label('Total Harga'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
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
}
