<?php

namespace App\Filament\Resources\AttractionStaffResource\Pages;

use App\Filament\Resources\AttractionStaffResource;
use App\Models\AttractionStaff;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAttractionStaff extends CreateRecord
{
    protected static string $resource = AttractionStaffResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $userId = $data['user_id'];
        $attractionIds = $data['attraction_ids'];
        
        // Remove any existing assignments for this user (in case of duplicates)
        AttractionStaff::where('user_id', $userId)->delete();
        
        // Create new assignments
        $firstRecord = null;
        foreach ($attractionIds as $attractionId) {
            $record = AttractionStaff::create([
                'user_id' => $userId,
                'attraction_id' => $attractionId,
            ]);
            
            // Keep the first record to return
            if (!$firstRecord) {
                $firstRecord = $record;
            }
        }
        
        // Return the first AttractionStaff record created
        return $firstRecord;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}