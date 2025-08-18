<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShiftResource\Pages;
use App\Filament\Resources\ShiftResource\RelationManagers;
use App\Models\Shift;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShiftResource extends Resource
{
    protected static ?string $model = Shift::class;

    protected static ?string $navigationIcon = 'fluentui-shifts-day-20';

     protected static ?string $navigationGroup = 'Manajemen Kantor';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                TextInput::make('name')
                    ->required()
                    ->label('Nama Shift'),
                TimePicker::make('start_time')
                    ->label('Waktu Mulai Shift')
                    ->required()
                    ->native(false) 
                    ->displayFormat('H:i') // Format tampilan di form
                    ->seconds(false), // Sembunyikan input detik
                TimePicker::make('end_time')
                    ->label('Waktu Selesai Shift')
                    ->required()
                    ->native(false) 
                    ->displayFormat('H:i') // Format tampilan di form
                    ->seconds(false), // Sembunyikan input detik
           
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('id')
                    ->label('Shift ID'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Shift'),
                TextColumn::make('start_time')
                    ->label('Waktu Mulai Shift')
                    ->dateTime('H:i'),
                TextColumn::make('end_time')
                    ->label('Waktu Selesai Shift')
                    ->dateTime('H:i'),
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
            'index' => Pages\ListShifts::route('/'),
            'create' => Pages\CreateShift::route('/create'),
            'edit' => Pages\EditShift::route('/{record}/edit'),
        ];
    }
}
