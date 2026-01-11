<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GoldPriceResource\Pages;
use App\Models\GoldPrice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Import semua komponen yang kita butuhkan
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;

class GoldPriceResource extends Resource
{
    protected static ?string $model = GoldPrice::class;

    protected static ?string $slug = 'harga-emas';

    protected static ?string $modelLabel = 'Harga Emas';

    protected static ?string $pluralModelLabel = 'Harga Emas Harian';

    // INI PENEMPATAN YANG BENAR
    protected static ?string $navigationGroup = 'Menejemen keuangan';
    protected static ?string $navigationLabel = 'Harga Emas Harian';
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('jenis_emas')
                    ->label('Jenis Emas')
                    ->options([
                        'Emas Tua' => 'Emas Tua',
                        'Emas Muda' => 'Emas Muda',
                    ])
                    ->required(),
                TextInput::make('harga_per_gram')
                    ->label('Harga per Gram')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->required()
                    ->default(now()) // Otomatis terisi tanggal hari ini
                    ->helperText('Setiap tanggal hanya boleh satu harga per jenis emas.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanggal')->date('d F Y')->sortable(),
                TextColumn::make('jenis_emas')->searchable(),
                TextColumn::make('harga_per_gram')->money('IDR')->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
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
            ])
            ->defaultSort('tanggal', 'desc'); // Urutkan dari yang terbaru
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
            'index' => Pages\ListGoldPrices::route('/'),
            'create' => Pages\CreateGoldPrice::route('/create'),
            'edit' => Pages\EditGoldPrice::route('/{record}/edit'),
        ];
    }
}