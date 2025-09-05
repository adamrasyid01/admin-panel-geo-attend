<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\Role;
use App\Models\User;
use Faker\Core\File;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-s-users';



    protected static ?string $navigationGroup = 'Manajemen Pengguna';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Name'),
                TextInput::make('email')
                    ->required()
                    ->label('Email'),
                TextInput::make('password')
                    ->required()
                    ->label('Password')
                    ->password(),
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->live(),
                    
                Select::make('position_id')
                    ->label('Posisi Jabatan')
                    ->relationship('position', 'name') // Asumsi relasi 'position' ada di model User
                    ->searchable()
                    ->preload()
                    ->visible(function (callable $get) {
                        $roleIds = $get('roles'); // Ambil ID role yang dipilih

                        if (empty($roleIds)) {
                            return false; // Sembunyikan jika tidak ada role yang dipilih
                        }

                        // Cek ke database apakah salah satu role yang dipilih adalah 'karyawan'
                        $roleNames = Role::whereIn('id', $roleIds)->pluck('name');
                        return $roleNames->contains('Staff');
                    }),
                FileUpload::make('face_embedding_id')
                    ->label('Face Embedding ID')
                    ->required()
                    ->acceptedFileTypes(['image/*'])
                    ->maxSize(1024), // 1MB

                Repeater::make('userShifts')
                    ->relationship()
                    ->schema([
                        Select::make('shift_id')
                            ->relationship('shift', 'name')
                            ->required()
                            ->label('Tambahkan Shift Karyawan'),
                    ])
                    ->visible(function (callable $get) {
                        $roleIds = $get('roles'); // array role_id dari select

                        if (empty($roleIds)) {
                            return false;
                        }

                        // Ambil semua nama role dari database
                        $roleNames = Role::where('id', $roleIds)->pluck('name');

                        // Hanya tampilkan kalau salah satu role adalah "karyawan"
                        return $roleNames->contains('karyawan');
                    }),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Name'),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->label('Email'),
                // Di dalam method table() di UserResource.php

                TextColumn::make('roles.name') // <-- UBAH DI SINI
                    ->label('Role')
                    ->sortable() // Opsional, agar bisa di-sort
                    ->searchable(), // Opsional, agar bisa di-searchr
                TextColumn::make('face_embedding_id')
                    ->label('Face Embedding ID'),

                TextColumn::make('position.name')
                    ->label('Posisi Jabatan')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
