<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WfhRequestResource\Pages;
use App\Filament\Resources\WfhRequestResource\RelationManagers;
use App\Models\WfhRequest;
use Dom\Text;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class WfhRequestResource extends Resource
{
    protected static ?string $model = WfhRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                    ->searchable() // Disarankan agar mudah mencari nama
                    ->preload()
                    ->required()
                    ->label('Karyawan yang Mengajukan'),
                DatePicker::make('tanggal')
                    ->required()
                    ->label('Tanggal WFH')
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d'),
                TextInput::make('reason')->label('Alasan WFH')
                    ->required()
                    ->maxLength(255),
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
                    ->hidden(fn(Get $get) => $get('status') !== 'approved')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListWfhRequests::route('/'),
            'create' => Pages\CreateWfhRequest::route('/create'),
            'edit' => Pages\EditWfhRequest::route('/{record}/edit'),
        ];
    }
}
