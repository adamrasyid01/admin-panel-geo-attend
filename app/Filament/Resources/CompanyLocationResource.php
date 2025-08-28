<?php

namespace App\Filament\Resources;

use Afsakar\LeafletMapPicker\LeafletMapPicker;
use App\Filament\Resources\CompanyLocationResource\Pages;
use App\Filament\Resources\CompanyLocationResource\RelationManagers;
use App\Models\CompanyLocation;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompanyLocationResource extends Resource
{
    protected static ?string $model = CompanyLocation::class;

    protected static ?string $navigationIcon = 'heroicon-c-building-office-2';



    protected static ?string $navigationGroup = 'Manajemen Kantor';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Select::make('user_id')
                    ->relationship(
                        name: 'user', // Nama relasi di model saat ini
                        titleAttribute: 'name', // Kolom nama dari tabel User

                        // Fungsi untuk memfilter daftar user yang akan ditampilkan
                        modifyQueryUsing: fn(Builder $query) => $query->whereHas('roles', fn(Builder $query) => $query->where('name', 'super_admin'))
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Pemilik Lokasi Perusahaan'),
                TextInput::make('name')
                    ->required()
                    ->label('Nama Perusahaan'),
                TextInput::make('address')
                    ->required()
                    ->label('Alamat Perusahaan'),
                LeafletMapPicker::make('location') // Nama harus sama dengan kolom JSON di DB
                    ->label('Pilih Lokasi Perusahaan di Peta')
                    ->height('400px') // Mengatur tinggi peta
                    ->defaultZoom(15) // Mengatur level zoom awal
                    ->defaultLocation([-7.2575, 112.7521]),
                TextInput::make('allowed_radius')
                    ->required()
                    ->numeric()
                    ->label('Radius Absen yang Diizinkan (dalam meter)'),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('id')
                    ->label('ID Lokasi Perusahaan'),
                TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label('Pemilik Lokasi Perusahaan'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Perusahaan'),
                TextColumn::make('address')
                    ->searchable()
                    ->label('Alamat Perusahaan'),
                TextColumn::make('allowed_radius')
                    ->label('Radius yang Diizinkan (m)'),
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
            'index' => Pages\ListCompanyLocations::route('/'),
            'create' => Pages\CreateCompanyLocation::route('/create'),
            'edit' => Pages\EditCompanyLocation::route('/{record}/edit'),
        ];
    }
}
