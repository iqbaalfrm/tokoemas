<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RiwayatNotifikasiResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Notifications\DatabaseNotification; // Model bawaan Laravel
use Illuminate\Database\Eloquent\Builder;

class RiwayatNotifikasiResource extends Resource
{
    // Arahkan ke Model bawaan tabel notifications
    protected static ?string $model = DatabaseNotification::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $navigationLabel = 'Riwayat Notifikasi';

    protected static ?string $modelLabel = 'Notifikasi';

    protected static ?string $navigationGroup = 'Laporan Keuangan'; 
    protected static ?int $navigationSort = 99;


    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('data.title')
                    ->label('Judul')
                    ->columnSpanFull(),
                
                Forms\Components\Textarea::make('data.body')
                    ->label('Pesan')
                    ->rows(3)
                    ->columnSpanFull(),
                
                Forms\Components\DateTimePicker::make('read_at')
                    ->label('Dibaca Pada'),
                
                Forms\Components\DateTimePicker::make('created_at')
                    ->label('Dibuat Pada'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('data.title')
                    ->label('Judul')
                    ->searchable()
                    ->weight('bold')
                    ->limit(50),

                Tables\Columns\TextColumn::make('data.body')
                    ->label('Pesan')
                    ->limit(100)
                    ->wrap(),

                Tables\Columns\TextColumn::make('notifiable.name')
                    ->label('Penerima')
                    ->sortable(),

                Tables\Columns\IconColumn::make('read_at')
                    ->label('Status')
                    ->icon(fn ($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-envelope')
                    ->color(fn ($state) => $state ? 'success' : 'warning')
                    ->tooltip(fn ($record) => $record->read_at ? 'Sudah dibaca: ' . $record->read_at : 'Belum dibaca'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('unread')
                    ->label('Belum Dibaca')
                    ->query(fn (Builder $query) => $query->whereNull('read_at')),
            ])
            ->actions([
  
                Tables\Actions\ViewAction::make()
                    ->label('Lihat Detail')
                    ->color('info'),
            ])
            ->bulkActions([
 
                Tables\Actions\BulkAction::make('markAsRead')
                    ->label('Tandai Sudah Dibaca')
                    ->icon('heroicon-o-check')
                    ->action(fn ($records) => $records->each->markAsRead())
                    ->deselectRecordsAfterCompletion(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRiwayatNotifikasis::route('/'),
        ];
    }
}