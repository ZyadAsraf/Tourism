<?php

namespace App\Filament\Resources;

use App\Models\NormalAdmin;
use Propaganistas\LaravelPhone\Rules\Phone;
use App\Filament\Resources\AdminResource\Pages;
use App\Filament\Resources\AdminResource\RelationManagers;
use Faker\Provider\ar_EG\Text;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AdminResource extends Resource
{
    protected static ?string $model = NormalAdmin::class;
     
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('FirstName')->required()->label('first name')->rules('max:49'),
                TextInput::make('LastName')->required()->label('last name'),
                TextInput::make('PhoneNumber')->numeric()->required()->label('phone number')->rules('phone:EG'),
                DatePicker::make('BirthDate')->required()->label('birth date'),
                TextInput::make('Email')->required()->email(),
                TextInput::make('Password')->password()->required()->rules([
                    'min:8',  
                    'regex:/[A-Z]/',
                    'regex:/[a-z]/',
                    'regex:/[0-9]/',
                    'max:249',
                ])
                ->validationMessages([
                    'min' => 'The password must be at least 8 characters long.',
                            'regex' => 'The password must contain at least one uppercase letter, one lowercase letter and one number'
                ]),
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
            'index' => Pages\ListAdmins::route('/'),
            'create' => Pages\CreateAdmin::route('/create'),
            'edit' => Pages\EditAdmin::route('/{record}/edit'),
        ];
    }
}
