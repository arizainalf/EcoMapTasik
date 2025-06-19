<?php
namespace App\Filament\Resources\OrderResource\Pages;

use Filament\Actions;
use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ListRecords;
use Asmit\ResizedColumn\HasResizableColumn;

class ListOrders extends ListRecords
{
    use HasResizableColumn;

    protected static string $resource = OrderResource::class;
    protected static ?string $title   = 'Pesanan';


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
