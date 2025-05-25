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
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Select a staff member'),

                Forms\Components\Select::make('attraction_id')
                    ->label('Attraction')
                    ->relationship('attraction', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Select an attraction'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Staff Member')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('attraction.name')
                    ->label('Attraction')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Assigned At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('attraction_id')
                    ->label('Attraction')
                    ->relationship('attraction', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Staff Member')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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