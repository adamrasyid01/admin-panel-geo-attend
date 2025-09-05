<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'jam-task-list-f';
     protected static ?string $navigationGroup = 'Manajemen Kantor';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //

                Select::make('user_id')
                     ->relationship(
                        name: 'user', // Nama relasi di model saat ini
                        titleAttribute: 'name', // Kolom yang ditampilkan dari tabel User

                        // 3. Tambahkan fungsi untuk memodifikasi query
                        modifyQueryUsing: fn(Builder $query) => $query->whereHas('roles', fn(Builder $query) => $query->where('name', 'karyawan'))
                    )
                    ->searchable() // Agar user bisa dicari
                    ->preload() // Memuat opsi di awal agar lebih cepat
                    ->required()
                    ->label('Ditugaskan Kepada'),

                // Field untuk nama tugas
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Tugas'),

                // Field untuk tenggat waktu
                DateTimePicker::make('deadline')
                    ->required()
                    ->native(false) // Menggunakan date picker kustom dari Filament, bukan bawaan browser
                    ->displayFormat('d/m/Y H:i')
                    ->label('Tenggat Waktu'),

                // Field untuk deskripsi tugas, dibuat lebih lebar
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull() // Membuat field ini memakan lebar 2 kolom
                    ->label('Deskripsi Tugas'),

                    
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                // Kolom untuk menampilkan nama tugas
                TextColumn::make('name')
                    ->label('Nama Tugas')
                    ->searchable() // Bisa dicari
                    ->sortable(), // Bisa diurutkan

                // Kolom untuk menampilkan nama user yang ditugaskan
                TextColumn::make('user.name')
                    ->label('Ditugaskan Kepada')
                    ->searchable()
                    ->sortable(),

                // Kolom untuk menampilkan tenggat waktu
                TextColumn::make('deadline')
                    ->label('Tenggat Waktu')
                    ->dateTime('d M Y, H:i') // Format tampilan tanggal dan waktu
                    ->sortable(),

                // Kolom untuk menampilkan siapa yang membuat (disembunyikan default)
                TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), // Bisa disembunyikan/ditampilkan

                // Kolom untuk menampilkan kapan dibuat (disembunyikan default)
                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
                SelectFilter::make('user_id')
                    ->label('Ditugaskan Kepada')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
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
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
