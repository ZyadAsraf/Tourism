<?php

namespace App\Filament\Resources\AttractionStaffResource\Pages;

use App\Filament\Resources\AttractionStaffResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttractionStaff extends EditRecord
{
    protected static string $resource = AttractionStaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
