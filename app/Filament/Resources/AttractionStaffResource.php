<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttractionStaffResource\Pages;
use App\Filament\Resources\AttractionStaffResource\RelationManagers;
use App\Models\AttractionStaff;
use App\Models\User;
use App\Models\Attraction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttractionStaffResource extends Resource
{
    protected static ?string $model = AttractionStaff::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Attraction Staff';

    protected static ?string $modelLabel = 'Staff Assignment';

    protected static ?string $pluralModelLabel = 'Staff Assignments';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Select::make('user_id')
                ->label('Staff Member')
                ->relationship(
                    'user', 
                    'username',
                    fn (Builder $query) => $query->whereHas('roles', function ($q) {
                        $q->where('name', 'Attraction_Staff');
                    })
                )
                ->searchable()
                ->preload()
                ->required()
                ->placeholder('Select a staff member'),

            Forms\Components\Select::make('attraction_ids')
                ->label('Attractions')
                ->multiple()
                ->options(Attraction::pluck('AttractionName', 'id'))
                ->searchable()
                ->preload()
                ->required()
                ->placeholder('Select attractions')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        // Get unique user_ids first to work with
        $staffUserIds = AttractionStaff::distinct()->pluck('user_id')->toArray();
        
        return $table
            ->query(
                // Start with user query to get one row per user
                User::query()
                    ->whereIn('id', $staffUserIds)
                    ->with(['roles'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('username')
                    ->label('Staff Member')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('assigned_attractions')
                    ->label('Assigned Attractions')
                    ->formatStateUsing(function ($record) {
                        // Get attractions directly from the user
                        $assignments = AttractionStaff::where('user_id', $record->id)
                            ->join('attractions', 'attraction_staff.attraction_id', '=', 'attractions.id')
                            ->pluck('attractions.AttractionName')
                            ->toArray();
                        
                        return !empty($assignments) ? implode(', ', $assignments) : 'None';
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('assignments_count')
                    ->label('Total Assignments')
                    ->formatStateUsing(function ($record) {
                        // Count assignments directly for this user
                        return AttractionStaff::where('user_id', $record->id)->count();
                    })
                    ->sortable(),               
                    
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('attractions')
                ->label('Has Assignment to Attraction')
                ->options(Attraction::pluck('AttractionName', 'id'))
                ->query(function (Builder $query, array $data) {
                    if (filled($data['value'])) {
                        // Filter users that have assignments to this attraction
                        $query->whereIn('id', function ($subQuery) use ($data) {
                            $subQuery->select('user_id')
                                ->from('attraction_staff')
                                ->where('attraction_id', $data['value']);
                        });
                    }
                }),
        ])
        ->actions([
            Tables\Actions\Action::make('edit_assignments')
                ->label('Edit Assignments')
                ->url(function ($record) {
                    // Get the first assignment for this user to use as our record ID
                    $firstAssignment = AttractionStaff::where('user_id', $record->id)
                        ->orderBy('id')
                        ->first();
                        
                    return $firstAssignment 
                        ? static::getUrl('edit', ['record' => $firstAssignment->id])
                        : '#';
                }),
            Tables\Actions\DeleteAction::make()
                ->label('Remove All Assignments')
                ->action(function ($record) {
                    // Delete all assignments for this staff member
                    AttractionStaff::where('user_id', $record->id)->delete();
                })
                ->requiresConfirmation()
                ->modalHeading('Remove All Assignments')
                ->modalDescription('Are you sure you want to remove all attraction assignments for this staff member?'),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('Remove All Assignments for Selected Staff')
                    ->action(function ($records) {
                        $userIds = $records->pluck('id');
                        AttractionStaff::whereIn('user_id', $userIds)->delete();
                    }),
            ]),
        ])
        ->defaultSort('username', 'asc');
}

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttractionStaff::route('/'),
            'create' => Pages\CreateAttractionStaff::route('/create'),
            'edit' => Pages\EditAttractionStaff::route('/{record}/edit'),
        ];
    }
}