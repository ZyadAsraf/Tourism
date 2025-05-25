<?php

namespace App\Filament\Resources\AttractionStaffResource\Pages;

use App\Filament\Resources\AttractionStaffResource;
use App\Models\AttractionStaff;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttractionStaff extends EditRecord
{
    protected static string $resource = AttractionStaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Remove All Assignments')
                ->action(function () {
                    // Delete all assignments for this staff member
                    AttractionStaff::where('user_id', $this->record->user_id)->delete();
                })
                ->requiresConfirmation()
                ->modalHeading('Remove All Assignments')
                ->modalDescription('Are you sure you want to remove all attraction assignments for this staff member?')
                ->successRedirectUrl(AttractionStaffResource::getUrl('index')),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Get the assignment record by ID (which is passed in the URL)
        $assignment = AttractionStaff::with(['user', 'attraction'])->find($this->record->id);
        
        if (!$assignment) {
            abort(404, 'Assignment not found');
        }

        // Get all attraction assignments for this user
        $userAssignments = AttractionStaff::where('user_id', $assignment->user_id)->get();
        
        return [
            'user_id' => $assignment->user_id,
            'attraction_ids' => $userAssignments->pluck('attraction_id')->toArray(),
        ];
    }

    protected function handleRecordUpdate($record, array $data): AttractionStaff
    {
        // Get the user_id from the assignment record
        $assignment = AttractionStaff::find($record->id);
        $userId = $assignment->user_id;

        // Delete all existing assignments for this user
        AttractionStaff::where('user_id', $userId)->delete();

        // Create new assignments
        $firstAssignment = null;
        foreach ($data['attraction_ids'] as $attractionId) {
            $newAssignment = AttractionStaff::create([
                'user_id' => $userId,
                'attraction_id' => $attractionId,
            ]);
            
            // Keep track of the first assignment to return
            if (!$firstAssignment) {
                $firstAssignment = $newAssignment;
            }
        }

        // Return the first assignment (or the original if no new ones were created)
        return $firstAssignment ?: $assignment;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}