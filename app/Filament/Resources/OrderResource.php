<?php
namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\Pages\OrderReport;
use App\Filament\Resources\OrderResource\RelationManagers\OrderProductsRelationManager;
use App\Models\Order;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Routing\Route;

class OrderResource extends Resource
{

    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Transaction Data';

    protected static ?string $navigationLabel = 'Pesanan';

    public static function getRoutes(): Closure
    {
        return function () {
            Route::get('/report', OrderReport::class)->name('report');
        };
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make([
                    Forms\Components\TextInput::make('user_id')
                        ->required()
                        ->numeric(),

                    Forms\Components\TextInput::make('total_price')
                        ->required()
                        ->numeric(),

                    Forms\Components\TextInput::make('bank_account_id') // periksa nama kolom di DB
                        ->numeric(),

                    Forms\Components\DateTimePicker::make('paid_at'),

                    Forms\Components\TextInput::make('courier')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('address_id')
                        ->required()
                        ->numeric(),

                    Forms\Components\TextInput::make('tracking_number')
                        ->maxLength(255),

                    Forms\Components\Select::make('status')
                        ->required()
                        ->options([
                            'pending' => 'Pending',
                            'paid'    => 'Dibayar',
                            'sent'    => 'Dikirim',
                        ])
                        ->default('pending'),

                    Forms\Components\FileUpload::make('payment_proof')
                        ->label('Bukti Pembayaran')
                        ->disk('public')
                        ->directory('payment_proofs')
                        ->image(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pembeli')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->money('IDR', locale: 'id')
                    ->label('Total Harga')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bankAccount.bank_name')
                    ->numeric()
                    ->label('Bank')
                    ->sortable(),
                Tables\Columns\ImageColumn::make('payment_proof')
                    ->label('Bukti Bayar')
                    ->height(100)
                    ->action(
                        Tables\Actions\Action::make('viewImage')
                            ->modalContent(fn($record) => view('tables.columns.payment-proof-modal', [
                                'image' => $record->payment_proof,
                            ]))
                            ->modalHeading('Bukti Pembayaran')
                            ->modalSubmitAction(false) // Hilangkan tombol Kirim
                            ->modalCancelAction(false)
                    ),
                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Tanggal Bayar')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('courier')
                    ->label('Kurir')
                    ->formatStateUsing(fn(string $state): string => ucfirst(strtolower($state)))
                    ->searchable(),
                Tables\Columns\TextColumn::make('address.full_address')
                    ->numeric()
                    ->label('Alamat')
                    ->sortable(),
                Tables\Columns\TextInputColumn::make('tracking_number')
                    ->label('Nomor Resi')
                    ->searchable(),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'pending' => '⏳ Pending',
                        'paid'    => '✅ Dibayar',
                        'sent'    => '📦 Dikirim',
                    ])
                    ->selectablePlaceholder(false)
                    ->searchable()
                    ->sortable()
                    ->label('Status')
                    ->extraAttributes([
                        'class' => 'font-medium',
                    ]),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ->recordUrl(null)
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            OrderProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrders::route('/'),
            'view'   => Pages\ViewOrder::route('/{record}'),
            'report' => Pages\OrderReport::route('/report'),
        ];
    }

}
