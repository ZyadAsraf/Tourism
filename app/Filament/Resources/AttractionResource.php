<?php

namespace App\Filament\Resources;
use App\Models\NormalAdmin;
use App\Models\TicketType;
use Filament\Forms\Components\Checkbox;
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
                TextInput::make('AttractionName')->required()->rules('max:49'),
                MarkdownEditor::make('Description')->required(),
                TextInput::make('Address')->rules('max:49'),
                TextInput::make('City')->rules('max:49'),
                TextInput::make('Street')->rules('max:49'),
                TextInput::make('Location Link')->url(),
                FileUpload::make('Img')->required()->directory('Imgs'),
                TextInput::make('EntryFee')->numeric()->rules('min:0')->validationMessages(['min'=>'The entry fee cannot be negative.'])->required(),
                Select::make('AdminId')->options(NormalAdmin::pluck('Email','id'))->required(),
                Select::make('GovernorateId')->options(Governorate::pluck('Name','id'))->required(),
                Select::make('TicketTypesId')->options(TicketType::pluck( 'title' , 'id'))->required(),
                Select::make('Status')->options(['Available'=>'Available' ,'Not available'=>'Not available']),
                CheckboxList::make('Categories')->relationship('categories','Name')
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('AttractionName'),
                TextColumn::make('EntryFee'),
                TextColumn::make('normal_admin.Email')->label('Admin'),
                TextColumn::make('governorate.Name')->label('Governrates'),
                TextColumn::make('ticketType.Title')->label('Ticket type'),
                TextColumn::make('Status'),
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
