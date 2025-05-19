<?php

namespace App\Filament\Resources\ArticleResource\RelationManagers;

use App\Models\Attraction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class articleLinksRelationManager extends RelationManager
{
    protected static string $relationship = 'attractions';

    protected static ?string $recordTitleAttribute = 'AttractionName';
    
    protected static ?string $title = 'Linked Attractions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('attraction_id')
                    ->label('Attraction')
                    ->options(Attraction::pluck('AttractionName', 'id'))
                    ->searchable()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('AttractionName')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('City')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('Img')
                    ->label('Image')
                    ->disk('public'),
                Tables\Columns\TextColumn::make('governorate.Name')
                    ->label('Governorate'),
            ])
            ->filters([
                // No filters needed for now
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->label('Link Attraction'),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->label('Remove Link'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make()
                        ->label('Remove Selected Links'),
                ]),
            ]);
    }
}