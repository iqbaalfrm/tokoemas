<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GoldPurityResource\Pages;
use App\Filament\Resources\GoldPurityResource\RelationManagers;
use App\Models\GoldPurity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GoldPurityResource extends Resource
{
    protected static ?string $model = GoldPurity::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Kadar Emas';
    protected static ?string $navigationGroup = 'Menejemen Produk';
    protected static ?string $pluralModelLabel = 'Kadar Emas';

    protected static ?string $modelLabel = 'Kadar Emas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nama Kadar'),
                Forms\Components\Textarea::make('description')
                    ->label('Keterangan'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kadar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Keterangan'),
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
            'index' => Pages\ListGoldPurities::route('/'),
            'create' => Pages\CreateGoldPurity::route('/create'),
            'edit' => Pages\EditGoldPurity::route('/{record}/edit'),
        ];
    }
}
