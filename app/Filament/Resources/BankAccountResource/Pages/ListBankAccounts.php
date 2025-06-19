<?php

namespace App\Filament\Resources\BankAccountResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Asmit\ResizedColumn\HasResizableColumn;
use App\Filament\Resources\BankAccountResource;

class ListBankAccounts extends ListRecords
{
    use HasResizableColumn;

    protected static string $resource = BankAccountResource::class;
    protected static ?string $title   = 'Bank';


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
