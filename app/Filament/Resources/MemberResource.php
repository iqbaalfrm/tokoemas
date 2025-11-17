<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Models\Member;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Illuminate\Database\Eloquent\Builder;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationGroup = 'Manajemen Membership';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Member';
    protected static ?string $slug = 'members';
    protected static ?string $modelLabel = 'Member';

    // --- FUNGSI BARU UNTUK MENGAMBIL DATA RELASI ---
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withCount('transactions') // Menghitung 'Berapa Kali Beli'
            ->withSum('transactionItems', 'weight_gram'); // Menjumlahkan 'Total Berat'
            // Pastikan relasi 'transactionItems' ada di model Member.php
            // public function transactionItems() { 
            //     return $this->hasManyThrough(TransactionItem::class, Transaction::class); 
            // }
    }
    // --- AKHIR FUNGSI BARU ---

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            TextInput::make('nama')
                ->required()
                ->label('Nama Member'),
            TextInput::make('no_hp')
                ->required()
                ->label('Nomor HP'),
            TextInput::make('alamat')
                ->label('Alamat')
                ->columnSpanFull(),
            TextInput::make('email')
                ->label('Email'),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('no_hp')
                    ->label('Nomor HP')
                    ->searchable(),
                
                // --- KOLOM BARU ---
                TextColumn::make('transactions_count')
                    ->label('Jumlah Beli')
                    ->sortable(),
                
                TextColumn::make('transaction_items_sum_weight_gram')
                    ->label('Total Berat (g)')
                    ->numeric(3)
                    ->suffix(' g')
                    ->sortable(),
                // --- AKHIR KOLOM BARU ---

                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            MemberResource\RelationManagers\TransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'view' => Pages\ViewMember::route('/{record}'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }

    
}