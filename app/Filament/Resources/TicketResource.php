<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\DateColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = "Tickets";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('PhoneNumber')
                    ->label('Phone Number')
                    ->required()
                    ->tel(),

                TextInput::make('BookingTime')
                    ->label('Booking Time')
                    ->required()
                    ->type('datetime-local'),

                TextInput::make('Quantity')
                    ->required()
                    ->numeric(),

                TextInput::make('TotalCost')
                    ->label('Total Cost')
                    ->required()
                    ->numeric(),

                Select::make('state')
                    ->required()
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Cancelled',
                    ]),

                DatePicker::make('VisitDate')
                    ->label('Visit Date')
                    ->required(),
    Select::make('Attraction')
    ->label('Attraction')
    ->required()
    ->relationship('attraction', 'AttractionName'),

Select::make('TicketTypesId')
    ->label('Ticket Type')
    ->required()
    ->relationship('ticketType', 'Title'),

Select::make('AttractionStaffId')
    ->label('Attraction Staff')
    ->required()
    ->relationship('attractionStaff', 'username'),

Select::make('TouristId')
    ->label('Tourist')
    ->required()
    ->relationship('tourist', 'username'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                
               TextColumn::make('tourist.username')
    ->label('Tourist')
    ->searchable(),

TextColumn::make('Quantity')
    ->label('Quantity')
    ->sortable(),

TextColumn::make('PhoneNumber')
    ->label('Phone Number')
    ->searchable(),

TextColumn::make('BookingTime')
    ->label('Booking Time')
    ->dateTime()
    ->sortable(),

TextColumn::make('VisitDate')
    ->label('Visit Date')
    ->date()
    ->sortable(),

TextColumn::make('TotalCost')
    ->label('Total Cost')
    ->sortable()
    ->money('USD'),

TextColumn::make('state')
    ->label('State')
    ->sortable(),

TextColumn::make('ticketType.Title')
    ->label('Ticket Type')
    ->sortable(),

TextColumn::make('attractionStaff.username')
    ->label('Attraction Staff')
    ->sortable(),

            ])
            ->filters([
                // Add filters here if needed
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
            // Add relation managers if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
        ];
    }
}
