<?php

namespace App\Filament\Resources\GovernoratesResource\Pages;

use App\Filament\Resources\GovernoratesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGovernorates extends ListRecords
{
    protected static string $resource = GovernoratesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
