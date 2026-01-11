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

    protected static ?string $slug = 'member';

    protected static ?string $modelLabel = 'Member';

    protected static ?string $pluralModelLabel = 'Member';

    protected static ?string $navigationGroup = 'Menejemen Membership';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Member';

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
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),
                TextColumn::make('no_hp')
                    ->label('Nomor HP')
                    ->searchable(),
                
                // --- KOLOM BARU (Hidden on Mobile, Visible on Desktop) ---
                TextColumn::make('transactions_count')
                    ->label('Jml Beli')
                    ->sortable()
                    ->visibleFrom('md'),
                
                TextColumn::make('transaction_items_sum_weight_gram')
                    ->label('Tot.Berat')
                    ->numeric(2)
                    ->suffix(' g')
                    ->sortable()
                    ->visibleFrom('md'),
                // --- AKHIR KOLOM BARU ---

                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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