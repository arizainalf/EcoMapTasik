<?php

namespace App\Filament\Resources\SerapanSampahResource\Pages;

use App\Filament\Resources\SerapanSampahResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSerapanSampahs extends ListRecords
{
    protected static string $resource = SerapanSampahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
