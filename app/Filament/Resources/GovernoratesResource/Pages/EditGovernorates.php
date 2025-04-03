<?php

namespace App\Filament\Resources\GovernoratesResource\Pages;

use App\Filament\Resources\GovernoratesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGovernorates extends EditRecord
{
    protected static string $resource = GovernoratesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
