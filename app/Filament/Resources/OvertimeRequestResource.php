<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OvertimeRequestResource\Pages;
use App\Filament\Resources\OvertimeRequestResource\RelationManagers;
use App\Models\LeaveRequest;
use App\Models\OvertimeRequest;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class OvertimeRequestResource extends Resource
{
    protected static ?string $model = OvertimeRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Manajemen Perizinan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship(
                        name: 'user', // Nama relasi di model saat ini
                        titleAttribute: 'name', // Kolom yang ditampilkan dari tabel User

                        // 3. Tambahkan fungsi untuk memodifikasi query
                        modifyQueryUsing: fn(Builder $query) => $query->whereHas('role', fn(Builder $query) => $query->where('name', 'karyawan'))
                    )
                    ->searchable() // Disarankan agar mudah mencari nama
                    ->preload()
                    ->required()
                    ->label('Karyawan yang Mengajukan'),
                DatePicker::make('date')
                    ->required()
                    ->label('Tanggal Lembur')
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d'),
                TimePicker::make('start_time')
                    ->label('Waktu Mulai Lembur')
                    ->required()
                    ->native(false) 
                    ->displayFormat('H:i') 
                    ->seconds(false), 
                TimePicker::make('end_time')
                    ->label('Waktu Selesai Lembur')
                    ->required()
                    ->native(false) 
                    ->displayFormat('H:i') 
                    ->seconds(false), 
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required()
                    ->label('Status Permohonan')
                    ->live() // Membuat field ini reaktif
                    ->afterStateUpdated(function (Set $set, $state) {
                        // Jika status diubah menjadi 'approved'
                        if ($state === 'approved') {
                            // Isi field 'approved_by' dengan ID user yang sedang login
                            $set('approved_by', Auth::id());
                        } else {
                            // Jika status lain, kosongkan field 'approved_by'
                            $set('approved_by', null);
                        }
                    })
                    ->hiddenOn('create'), // Tetap disembunyikan saat create

                // 2. TAMBAHKAN FIELD UNTUK MENAMPILKAN 'APPROVED_BY'
                Select::make('approved_by')
                    ->label('Disetujui Oleh')
                    ->relationship('approvedBy', 'name') // Menggunakan relasi yang sudah dibuat
                    ->disabled() // Field ini tidak bisa diubah manual
                    ->dehydrated() // Pastikan nilainya tetap tersimpan meski disabled
                    // 3. SEMBUNYIKAN SECARA KONDISIONAL
                    ->hidden(fn(Get $get) => $get('status') !== 'approved'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('No'),
                TextColumn::make('user.name')->label('Karyawan'),
                TextColumn::make('date')->label('Tanggal Lembur'),
                TextColumn::make('start_time')->label('Waktu Mulai Lembur')->dateTime('H:i'),
                TextColumn::make('end_time')->label('Waktu Selesai Lembur')->dateTime('H:i'),
                TextColumn::make('status')
                    ->label('Status')
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    }),
                TextColumn::make('approvedBy.name')
                    ->label('Disetujui Oleh')
                    ->default('-')
                    // Ganti `LeaveRequest $record` menjadi `?LeaveRequest $record`
                    // dan tambahkan pengecekan null
                    ->hidden(function (?OvertimeRequest $record): bool {
                        // Jika tidak ada record (misal: saat render header), JANGAN sembunyikan kolom
                        if ($record === null) {
                            return false;
                        }
                        // Jalankan logika seperti biasa jika ada record
                        return $record->status !== 'approved';
                    }),
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
            'index' => Pages\ListOvertimeRequests::route('/'),
            'create' => Pages\CreateOvertimeRequest::route('/create'),
            'edit' => Pages\EditOvertimeRequest::route('/{record}/edit'),
        ];
    }
}
