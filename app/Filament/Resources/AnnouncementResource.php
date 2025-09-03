<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnouncementResource\Pages;
use App\Filament\Resources\AnnouncementResource\RelationManagers;
use App\Models\Announcement;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AnnouncementResource extends Resource
{
    protected static ?string $model = Announcement::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Select::make('user_id')->label('Pembuat Pengumuman')
                   ->required()
                   ->relationship(
                    name:'user',
                    titleAttribute: 'name',
                    modifyQueryUsing: fn(Builder $query) => $query->whereHas('roles', fn(Builder $query) => $query->where('name', 'super_admin'))
                   ),
                TextInput::make('title')->label('Judul Pengumuman')
                    ->required()
                    ->maxLength(255),
                Textarea::make('content')->label('Isi Pengumuman')
                    ->required(),
                FileUpload::make('attachment_path')->label('Lampiran (Optional)')
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(1024),

                Toggle::make('is_published')->label('Umumkan ke Publik')
                    ->required()
                    ->default(true)->inline(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('title')->label('Judul Pengumuman')->searchable()->sortable(),
                TextColumn::make('user.name')->label('Pembuat Pengumuman')->searchable()->sortable(),
                TextColumn::make('content')->label('Isi'),
                TextColumn::make('created_at')->label('Dibuat Pada')->dateTime('d/m/Y H:i')->sortable(),
                ToggleColumn::make('is_published')->label('Umumkan ke Publik')->sortable(),

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
            'index' => Pages\ListAnnouncements::route('/'),
            'create' => Pages\CreateAnnouncement::route('/create'),
            'edit' => Pages\EditAnnouncement::route('/{record}/edit'),
        ];
    }
}
