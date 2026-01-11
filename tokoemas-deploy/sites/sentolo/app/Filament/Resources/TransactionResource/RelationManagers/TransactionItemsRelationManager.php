<?php

namespace App\Filament\Resources\TransactionResource\RelationManagers;

use Filament\Forms; 
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\TransactionItem;
use Filament\Tables\Columns\Summarizers\Sum; 
use Filament\Resources\RelationManagers\RelationManager;

class TransactionItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactionItems';

    protected static ?string $title = 'Transaction Items'; 

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('productWithTrashed.name') 
            ->columns([
                Tables\Columns\TextColumn::make('productWithTrashed.category.name') 
                    ->label('Jenis'),

                Tables\Columns\TextColumn::make('productWithTrashed.name') 
                    ->label('Model'),

                Tables\Columns\TextColumn::make('productWithTrashed.gold_type') 
                    ->label('Kadar'),

                Tables\Columns\TextColumn::make('productWithTrashed.barcode') 
                    ->label('Kode')
                    ->placeholder('-'), 

                Tables\Columns\TextColumn::make('productWithTrashed.weight_gram') 
                    ->label('Berat')
                    ->suffix(' Gr') 
                    ->numeric(3), 

                Tables\Columns\TextColumn::make('price') 
                    ->label('Harga')
                    ->money('IDR', true)
                    ->summarize(Sum::make()->money('IDR', true)->label('Total')), 
            ])
            ->filters([
                
            ])
            ->headerActions([
                 
            ])
            ->actions([
                 
            ])
            ->bulkActions([
                 
            ])
            ->paginated(false); 
    }
}