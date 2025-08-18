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
                Select::make('role_id')
                    ->relationship('role', 'name')->required()->live(),
                FileUpload::make('face_embedding_id')
                    ->label('Face Embedding ID')
                    ->required()
                    ->acceptedFileTypes(['image/*'])
                    ->maxSize(1024), // 1MB

                Repeater::make('userShifts')->relationship()
                    ->schema([
                        Select::make('shift_id')
                            ->relationship('shift', 'name')
                            ->required()->label('Tambahkan Shift Karyawan'),

                    ])
                    ->disabled(function (callable $get) {
                        // Ambil role_id yang sedang dipilih
                        $roleId = $get('role_id');

                        // Jika tidak ada role yang dipilih, sembunyikan repeater
                        if (!$roleId) {
                            return true;
                        }

                        // Cari nama role berdasarkan ID
                        $roleName = Role::find($roleId)?->name;

                        // Sembunyikan jika nama role adalah 'admin'
                        // Tampilkan jika nama role adalah 'karyawan' atau lainnya
                        return $roleName === 'admin';
                    })
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

                TextColumn::make('role.name') // <-- UBAH DI SINI
                    ->label('Role')
                    ->sortable() // Opsional, agar bisa di-sort
                    ->searchable(), // Opsional, agar bisa di-searchr
                TextColumn::make('face_embedding_id')
                    ->label('Face Embedding ID'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
