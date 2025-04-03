<?php

namespace App\Filament\Resources;
use Filament\Forms\Components\Checkbox;
use App\Models\Admin;
use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\AttractionResource\Pages;
use App\Filament\Resources\AttractionResource\RelationManagers;
use App\Models\Attraction;
use App\Models\Governorate;
use App\Models\Ticket_Type;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use League\Csv\Buffer;

class AttractionResource extends Resource
{
    protected static ?string $model = Attraction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('Attraction_Name')->required()->rules('max:49'),
                MarkdownEditor::make('Description')->required(),
                TextInput::make('address')->rules('max:49'),
                TextInput::make('city')->rules('max:49'),
                TextInput::make('street')->rules('max:49'),
                TextInput::make('location_link')->url(),
                FileUpload::make('img')->required()->directory('Imgs'),
                TextInput::make('entryFee')->numeric()->rules('min:0')->validationMessages(['min'=>'The entry fee cannot be negative.'])->required(),
                Select::make('admin_id')->options(Admin::pluck('Email','id'))->required(),
                Select::make('governorate_id')->options(Governorate::pluck('Name','id'))->required(),
                Select::make('ticket_types_id')->options(Ticket_Type::pluck( 'title' , 'id'))->required(),
                Select::make('status')->options(['Available'=>'Available' ,'Not available'=>'Not available']),
                CheckboxList::make('categories')->relationship('categories','Name')
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('Attraction_Name'),
                TextColumn::make('entryFee'),
                TextColumn::make('admin.Email')->label('Admin'),
                TextColumn::make('governorate.Name')->label('Governrates'),
                TextColumn::make('ticketType.title')->label('Ticket type'),
                TextColumn::make('status'),
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
            'index' => Pages\ListAttractions::route('/'),
            'create' => Pages\CreateAttraction::route('/create'),
            'edit' => Pages\EditAttraction::route('/{record}/edit'),
        ];
    }
}
