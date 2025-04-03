<?php

namespace App\Filament\Resources\AttractionStaffResource\Pages;

use App\Filament\Resources\AttractionStaffResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttractionStaff extends ListRecords
{
    protected static string $resource = AttractionStaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
