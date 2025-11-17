<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Inventory;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Services\InventoryLabelService;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\InventoryResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class InventoryResource extends Resource implements HasShieldPermissions
{
    public static function getPermissionPrefixes(): array
    {
        return [
            'view_any',
            'create',
            'update',
            'delete_any',
        ];
    }

    protected static ?string $model = Inventory::class;
    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';
    protected static ?string $navigationLabel = 'Menejemen Inventori';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationGroup = 'Menejemen Produk';

    // public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::count();
    // }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Details')
                    ->schema([
                        Forms\Components\ToggleButtons::make('type')
                            ->label('Tipe Stok')
                            ->options(InventoryLabelService::getTypes())
                            ->colors([
                                'in' => 'success',
                                'out' => 'danger',
                                'adjustment' => 'info',
                            ])
                            ->default('in')
                            ->grouped()
                            ->live(),
                        Forms\Components\Select::make('source')
                            ->label('Sumber')
                            ->required()
                            ->options(fn (Get $get) => InventoryLabelService::getSourceOptionsByType($get('type'))),
                    ])->columns(3),
                
                Forms\Components\Section::make('Pemilihan Produk')->schema([
                    self::getItemsRepeater(),
                ]),

                Forms\Components\Section::make('Total')
                    ->schema([
                        Forms\Components\TextInput::make('total_modal')
                            ->label('Total Modal')
                            ->prefix('Rp')
                            ->numeric()
                            ->readOnly()
                            ->dehydrated(false), // Jangan simpan ini di tabel 'inventories'
                    ]),

                Forms\Components\Section::make('Catatan')->schema([
                    Forms\Components\Textarea::make('notes')
                        ->maxLength(255)
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('No.Referensi')
                    ->weight('semibold')
                    ->copyable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'in' => 'Masuk',
                        'out' => 'Keluar',
                        'adjustment' => 'Penyesuaian',
                        default => $state
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'in' => 'heroicon-o-arrow-down-circle',
                        'out' => 'heroicon-o-arrow-up-circle',
                        'adjustment' => 'heroicon-o-arrow-path-rounded-square',
                        default => ''
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'in' => 'success',
                        'out' => 'danger',
                        'adjustment' => 'info',
                        default => 'gray'
                    }),
                Tables\Columns\TextColumn::make('source')
                    ->label('Sumber')
                    ->formatStateUsing(fn ($state, $record) => InventoryLabelService::getSourceLabel($record->type, $state)),
                Tables\Columns\TextColumn::make('notes')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('created_at', 'desc')
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

    public static function getItemsRepeater(): Repeater
    {
        return Repeater::make('inventoryItems')
            ->relationship('inventoryItems')
            ->live()
            ->afterStateUpdated(function (Get $get, Set $set) {
                self::updateTotalModal($get, $set);
            })
            ->columns(10)
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Produk')
                    ->required()
                    ->searchable(['name', 'sku'])
                    ->searchPrompt('Cari nama atau sku produk')
                    ->preload()
                    ->relationship('product', 'name')
                    ->getOptionLabelFromRecordUsing(fn (Product $record) => "{$record->name}-({$record->stock})-{$record->sku}")
                    ->columnSpan(4)
                    ->live()
                    ->afterStateHydrated(function (Forms\Set $set, Forms\Get $get, $state) {
                        $product = Product::find($state);
                        $set('stock', $product->stock ?? 0);
                        $cost_price = $get('cost_price') ?? 0;
                        $quantity = $get('quantity') ?? 1;
                        $set('total_cost_per_item', $cost_price * $quantity);
                    })
                    ->afterStateUpdated(function ($state, Forms\Set $set, Get $get) {
                        $product = Product::find($state);
                        $cost_price = $product->cost_price ?? 0;
                        $set('stock', $product->stock ?? 0);
                        $set('cost_price', $cost_price);
                        $set('quantity', 1);
                        $set('total_cost_per_item', $cost_price * 1);
                        self::updateTotalModal($get, $set);
                    })
                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                
                Forms\Components\TextInput::make('stock')
                    ->label('Stok Ada')
                    ->numeric()
                    ->readOnly()
                    ->columnSpan(1),

                Forms\Components\TextInput::make('cost_price')
                    ->label('Harga Modal')
                    ->numeric()
                    ->required()
                    ->prefix('Rp')
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $quantity = $get('quantity') ?? 0;
                        $cost_price = $get('cost_price') ?? 0;
                        $set('total_cost_per_item', $quantity * $cost_price);
                        self::updateTotalModal($get, $set);
                    })
                    ->columnSpan(2),

                Forms\Components\TextInput::make('quantity')
                    ->label('Jumlah')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $quantity = $get('quantity') ?? 0;
                        $cost_price = $get('cost_price') ?? 0;
                        $set('total_cost_per_item', $quantity * $cost_price);
                        self::updateTotalModal($get, $set);
                    })
                    ->columnSpan(1),

                Forms\Components\TextInput::make('total_cost_per_item')
                    ->label('Subtotal Modal')
                    ->numeric()
                    ->prefix('Rp')
                    ->readOnly()
                    ->dehydrated(false)
                    ->columnSpan(2),
            ]);
    }

    public static function updateTotalModal(Get $get, Set $set): void
    {
        $items = $get('inventoryItems');
        $total = 0;

        if (is_array($items)) {
            foreach ($items as $item) {
                $cost_price = $item['cost_price'] ?? 0;
                $quantity = $item['quantity'] ?? 0;
                $total += $cost_price * $quantity;
            }
        }
        
        $set('total_modal', $total);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInventories::route('/'),
            'edit' => Pages\EditInventory::route('/{record}/edit'),
        ];
    }
}