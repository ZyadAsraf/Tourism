<?php

namespace App\Filament\Resources;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\AttractionStaffResource\Pages;
use App\Filament\Resources\AttractionStaffResource\RelationManagers;
use App\Models\Attraction;
use App\Models\Attraction_Staff;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttractionStaffResource extends Resource
{
    protected static ?string $model = Attraction_Staff::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('FirstName')->required()->rules('max:49'),
                TextInput::make('LastName')->required()->rules('max:49'),
                TextInput::make('Email')->required()->rules('max:59')->email(),
                TextInput::make('Password')->password()->required()->rules([
                    'min:8',  
                    'regex:/[A-Z]/',
                    'regex:/[a-z]/',
                    'regex:/[0-9]/',
                    'max:254',
                ])
                ->validationMessages([
                    'min' => 'The password must be at least 8 characters long.',
                            'regex' => 'The password must contain at least one uppercase letter, one lowercase letter and one number'
                ]),
                DatePicker::make('BirthDate')->required(),
                TextInput::make('PhoneNumber')->numeric()->required()->label('phone number')->rules('phone:EG'),
                Select::make('attraction_id')->options(Attraction::pluck('Attraction_Name','id'))->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('FirstName'),
                TextColumn::make('LastName'),
                TextColumn::make('PhoneNumber'),
                TextColumn::make('BirthDate'),
                TextColumn::make('Email'),
                TextColumn::make('attraction.Attraction_Name')->label('Attraction'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
