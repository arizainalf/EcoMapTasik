<?php

namespace App\Filament\Resources\SerapanSampahResource\Pages;

use App\Filament\Resources\SerapanSampahResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSerapanSampah extends EditRecord
{
    protected static string $resource = SerapanSampahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
