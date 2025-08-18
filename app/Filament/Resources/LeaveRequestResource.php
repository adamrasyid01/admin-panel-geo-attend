<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveRequestResource\Pages;
use App\Filament\Resources\LeaveRequestResource\RelationManagers;
use App\Models\LeaveRequest;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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

class LeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;

    protected static ?string $navigationIcon = 'pepicon-leave-circle-filled';

    protected static ?string $navigationGroup = 'Manajemen Perizinan';

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
                        modifyQueryUsing: fn(Builder $query) => $query->whereHas('role', fn(Builder $query) => $query->where('name', 'karyawan'))
                    )
                    ->searchable() // Disarankan agar mudah mencari nama
                    ->preload()
                    ->required()
                    ->label('Karyawan yang Mengajukan'),
                Select::make('type')
                    ->options([
                        'izin' => 'Izin',
                        'cuti' => 'Cuti',
                        'sakit' => 'Sakit',
                    ])
                    ->required()
                    ->label('Tipe Permohonan'),
                DatePicker::make('start_date')
                    ->required()
                    ->label('Tanggal Mulai')
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d'),

                DatePicker::make('end_date')
                    ->required()
                    ->label('Tanggal Selesai')
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d'),

                Textarea::make('reason')
                    ->required()
                    ->label('Alasan Permohonan')
                    ->rows(3),
                FileUpload::make('attachment')
                    ->label('Lampiran')
                    ->preserveFilenames()
                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                    ->maxFiles(5),
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
                //
                TextColumn::make('id')->label('No'),
                TextColumn::make('user.name')->label('Karyawan'),
                TextColumn::make('type')->label('Tipe'),
                TextColumn::make('start_date')->label('Tanggal Mulai'),
                TextColumn::make('end_date')->label('Tanggal Selesai'),
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
                    ->hidden(function (?LeaveRequest $record): bool {
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
            'index' => Pages\ListLeaveRequests::route('/'),
            'create' => Pages\CreateLeaveRequest::route('/create'),
            'edit' => Pages\EditLeaveRequest::route('/{record}/edit'),
        ];
    }
}
