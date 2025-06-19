<?php
namespace App\Filament\Resources\ProductResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ProductResource;
use Asmit\ResizedColumn\HasResizableColumn;

class ListProducts extends ListRecords
{
    use HasResizableColumn;

    protected static string $resource = ProductResource::class;
    protected static ?string $title   = 'Produk';


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
