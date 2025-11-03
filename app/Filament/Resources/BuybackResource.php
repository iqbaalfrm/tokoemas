<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BuybackResource\Pages;
use App\Models\Buyback;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BuybackResource extends Resource
{
    protected static ?string $model = Buyback::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-left';
    protected static ?string $navigationLabel = 'Buyback';
    protected static ?string $modelLabel = 'Buyback';
    protected static ?string $navigationGroup = 'Menejemen Produk';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Buyback')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal')
                            ->required()
                            ->default(now()),
                        Forms\Components\ToggleButtons::make('tipe')
                            ->required()
                            ->inline()
                            ->options([
                                'pelanggan' => 'Pelanggan',
                                'pembelian_stok' => 'Pembelian Stok',
                            ])
                            ->colors([
                                'pelanggan' => 'info',
                                'pembelian_stok' => 'success',
                            ])
                            ->default('pelanggan'),
                        Forms\Components\TextInput::make('berat_total')
                            ->label('Berat Total (g)')
                            ->numeric()
                            ->readOnly()
                            ->default(0)
                            ->suffix('g'),
                    ])->columns(3),

                Forms\Components\Section::make('Barang yang di-Buyback')
                    ->schema([
                        Forms\Components\Repeater::make('buybackItems')
                            ->label('Item Buyback')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('nama_produk')
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('berat')
                                    ->numeric()
                                    ->required()
                                    ->suffix('g')
                                    ->live(onBlur: true),
                                Forms\Components\FileUpload::make('foto')
                                    ->disk('public')
                                    ->directory('buyback-photos')
                                    ->image()
                                    ->imageEditor()
                                    ->columnSpanFull(),
                            ])
                            ->columns(3)
                            ->addActionLabel('Tambahkan item buyback')
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $items = $get('buybackItems');
                                $totalWeight = 0;
                                if (is_array($items)) {
                                    foreach ($items as $item) {
                                        if (!empty($item['berat']) && is_numeric($item['berat'])) {
                                            $totalWeight += floatval($item['berat']);
                                        }
                                    }
                                }
                                $set('berat_total', $totalWeight);
                            }),
                    ]),

                Forms\Components\Section::make('Catatan')
                    ->schema([
                        Forms\Components\Textarea::make('catatan')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')->date('d M Y')->sortable(),
                BadgeColumn::make('tipe')
                    ->colors([
                        'info' => 'pelanggan',
                        'success' => 'pembelian_stok',
                    ])
                    ->formatStateUsing(fn (string $state): string => Str::title(str_replace('_', ' ', $state)))
                    ->searchable(),
                Tables\Columns\TextColumn::make('berat_total')->suffix(' g')->sortable(),
                Tables\Columns\TextColumn::make('buybackItems_count')->counts('buybackItems')->label('Jumlah Item'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('Lihat Foto')
                    ->icon('heroicon-o-photo')
                    ->color('gray')
                    ->modalContent(fn (Buyback $record): \Illuminate\View\View => view(
                        'filament.modals.view-buyback-photos', 
                        ['items' => $record->buybackItems()->whereNotNull('foto')->get()]
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListBuybacks::route('/'),
            'create' => Pages\CreateBuyback::route('/create'),
            'view' => Pages\ViewBuyback::route('/{record}'),
            'edit' => Pages\EditBuyback::route('/{record}/edit'),
        ];
    }    
}

