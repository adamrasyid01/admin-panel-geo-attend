<?php

namespace App\Filament\Resources;

use Afsakar\LeafletMapPicker\LeafletMapPicker;
use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\Attendance;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'fluentui-presence-available-10';

    protected static ?string $navigationGroup = 'Manajemen Kantor';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship(
                        name: 'user',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) => $query->whereHas('roles', fn(Builder $query) => $query->where('name', 'karyawan'))
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Nama Karyawan')
                    ->live() // Membuat field ini reaktif
                    ->afterStateUpdated(function (Set $set, $state) {
                        // $state adalah user_id yang dipilih
                        $user = User::with('userShifts.shift')->find($state);

                        if ($user && $user->userShifts) {
                            $set('user_shift_id', $user->userShifts->first()->id);
                        } else {
                            $set('user_shift_id', null);
                            $set('shift_name_display', null);
                        }
                    }),
                
                Hidden::make('user_shift_id')->required(),
                DateTimePicker::make('check_in_time')
                    ->label('Waktu Check In')
                    ->required()
                    ->native(false) // Tampilan UI lebih modern, tidak pakai bawaan browser
                    ->displayFormat('d F Y, H:i') // Format tampilan di form
                    ->seconds(false), // Sembunyikan input detik


                LeafletMapPicker::make('check_in_location') // Nama harus sama dengan kolom JSON di DB
                    ->label('Pilih Lokasi Check In di Peta')
                    ->height('400px') // Mengatur tinggi peta
                    ->defaultZoom(15) // Mengatur level zoom awal
                    ->defaultLocation([-7.2575, 112.7521]),
                LeafletMapPicker::make('check_out_location') // Nama harus sama dengan kolom JSON di DB
                    ->label('Pilih Lokasi Check Out di Peta')
                    ->height('400px') // Mengatur tinggi peta
                    ->defaultZoom(15) // Mengatur level zoom awal
                    ->defaultLocation([-7.2575, 112.7521]),

                DateTimePicker::make('check_out_time')
                    ->label('Waktu Check Out')
                    ->required()
                    ->native(false) // Tampilan UI lebih modern, tidak pakai bawaan browser
                    ->displayFormat('d F Y, H:i') // Format tampilan di form
                    ->seconds(false), // Sembunyikan input detik

                FileUpload::make('photo')->required()->label("Foto Kehadiran")
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('No'),
                TextColumn::make('user.name')->label('Karyawan'),
                TextColumn::make('check_in_time')->label('Waktu Check In')->dateTime('d F Y, H:i'),
                TextColumn::make('check_out_time')->label('Waktu Check Out')->dateTime('d F Y, H:i'),
                TextColumn::make('status')->label('Status') ->color(fn(string $state): string => match ($state) {
                        'Terlambat' => 'danger',
                        'Tepat Waktu' => 'success',
                    }),
                ImageColumn::make('photo')->label('Foto Kehadiran'),
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
