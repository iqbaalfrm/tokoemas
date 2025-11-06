<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\GoldPrice;
use App\Models\Product;
use App\Models\SubCategory; 
use Barryvdh\DomPDF\Facade\Pdf;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Milon\Barcode\DNS1D;
use App\Models\Approval;
use App\Models\User;
use App\Notifications\ApprovalDiminta;
use Filament\Notifications\Notification; 
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Support\Facades\Notification as LaravelNotification; 
use Filament\Support\Exceptions\Halt; 
use Illuminate\Support\Collection; 
use Illuminate\Support\Str; 

class ProductResource extends Resource implements HasShieldPermissions
{
    public static function getPermissionPrefixes(): array
    {
        return [
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'restore',
            'restore_any',
            'force_delete',
            'force_delete_any',
        ];
    }

    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';
    protected static ?string $navigationLabel = 'Produk';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Menejemen Produk';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ])->orderBy('created_at', 'desc');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2) 
                    ->schema([
                        // Field category_filter hanya untuk filtering, tidak disimpan
                        Forms\Components\Select::make('category_filter')
                            ->label('Kategori Produk')
                            ->options(Category::all()->pluck('name', 'id'))
                            ->searchable()
                            ->live() 
                            ->afterStateUpdated(fn (Set $set) => $set('sub_category_id', null))
                            ->dehydrated(false) // Tidak disimpan ke database
                            ->default(function (?Model $record) {
                                // Saat edit, set category berdasarkan sub_category yang dipilih
                                return $record?->subCategory?->category_id;
                            }),
                        
                        Forms\Components\Select::make('sub_category_id')
                            ->label('Sub-Kategori')
                            ->options(fn (Get $get): Collection => SubCategory::query()
                                ->where('category_id', $get('category_filter'))
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                                $subCategory = SubCategory::find($state);
                                $namaAwal = $subCategory?->name ?? '';
                                $set('name', $namaAwal . ' '); 
                                $set('sku', $subCategory?->code ?? ''); 
                            })
                            ->hidden(fn (Get $get) => !$get('category_filter')), 
                    ]),

                Forms\Components\TextInput::make('name')
                    ->label('Nama Produk')
                    ->required()
                    ->maxLength(255),

                    Forms\Components\Select::make('gold_type')
                    ->label('Jenis Emas')
                    ->options([
                        'Emas Tua' => 'Emas Tua',
                        'Emas Muda' => 'Emas Muda',
                    ])
                    ->live()
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updatePricesAndProfit($get, $set))
                    ->required(),

                Forms\Components\Select::make('gold_karat')
                    ->label('Kadar Emas')
                    ->options([
                        '8K' => '8 Karat (33.3%)',
                        '9K' => '9 Karat (37.5%)',
                        '10K' => '10 Karat (41.7%)',
                        '14K' => '14 Karat (58.5%)',
                        '18K' => '18 Karat (75%)',
                        '22K' => '22 Karat (91.7%)',
                        '24K' => '24 Karat (99.9%)',
                    ])
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('weight_gram')
                    ->label('Berat (gram)')
                    ->numeric()
                    ->step('0.01')
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updatePricesAndProfit($get, $set))
                    ->required(),

                Forms\Components\TextInput::make('selling_price')
                    ->label('Harga Jual Otomatis')
                    ->prefix('Rp')
                    ->readOnly()
                    ->numeric(),

                Forms\Components\FileUpload::make('image')
                    ->label('Gambar Produk')
                    ->directory('products')
                    ->disk('public')
                    ->helperText('Jika tidak diisi akan diisi foto default')
                    ->image(),

                Forms\Components\TextInput::make('stock')
                    ->label('Stok Produk')
                    ->helperText('Stok hanya dapat diisi/ditambah pada menejemen inventori')
                    ->required()
                    ->numeric()
                    ->readOnly()
                    ->default(0),

                Forms\Components\TextInput::make('sku')
                    ->label('SKU (Kode)')
                    ->helperText('Otomatis terisi dari Sub-Kategori')
                    ->maxLength(255)
                    ->readOnly(), 

                Forms\Components\TextInput::make('barcode')
                    ->label('Kode Barcode')
                    ->numeric()
                    ->helperText('Jika tidak diisi akan di generate otomatis')
                    ->maxLength(255),

                Forms\Components\Toggle::make('is_active')
                    ->label('Produk Aktif')
                    ->default(true)
                    ->required(),

                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi Produk')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Produk')
                    ->description(fn(Product $record): string => $record->subCategory?->category?->name . ' - ' . $record->subCategory?->name ?? 'Tanpa Kategori')
                    ->searchable(),
                    Tables\Columns\TextColumn::make('gold_karat')
                    ->label('Kadar')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image')
                    ->disk('public')
                    ->label('Gambar')
                    ->circular(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost_price')
                    ->label('Harga Modal')
                    ->prefix('Rp ')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), 
                Tables\Columns\TextColumn::make('selling_price')
                    ->label('Harga Saat Ini')
                    ->money('IDR', true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('barcode')
                    ->label('No.Barcode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Produk Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('sub_category_id')
                    ->label('Sub-Kategori')
                    ->relationship('subCategory', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('Reset Stok')
                    ->action(fn(Product $record) => $record->update(['stock' => 0]))
                    ->button()
                    ->color('info')
                    ->requiresConfirmation(),
                Tables\Actions\EditAction::make()->button(),
                Tables\Actions\DeleteAction::make()
                    ->action(function (Product $record) { 
                        if (auth()->user()->hasRole('super_admin')) {
                            $record->delete();
                            Notification::make()->title('Produk dihapus (sementara).')->success()->send();
                            return;
                        }
                        if (auth()->user()->hasRole('admin')) {
                            $approval = Approval::create([
                                'user_id' => auth()->id(),
                                'approvable_type' => Product::class,
                                'approvable_id' => $record->id,
                                'changes' => ['action' => 'delete'], 
                                'status' => 'pending',
                            ]);
                            $superAdmins = User::role('super_admin')->get();
                            if ($superAdmins->isNotEmpty()) {
                                LaravelNotification::send($superAdmins, new ApprovalDiminta($approval));
                            }
                            Notification::make()->title('Menunggu Approval')->body('Permintaan hapus produk (sementara) telah dikirim ke Superadmin.')->success()->send();
                            throw new Halt(); 
                        }
                    }),
                Tables\Actions\ForceDeleteAction::make()
                    ->action(function (Product $record) { 
                        if (auth()->user()->hasRole('super_admin')) {
                            $record->forceDelete();
                            Notification::make()->title('Produk dihapus permanen.')->success()->send();
                            return;
                        }
                        if (auth()->user()->hasRole('admin')) {
                            $approval = Approval::create([
                                'user_id' => auth()->id(),
                                'approvable_type' => Product::class,
                                'approvable_id' => $record->id,
                                'changes' => ['action' => 'force_delete'], 
                                'status' => 'pending',
                            ]);
                            $superAdmins = User::role('super_admin')->get();
                            if ($superAdmins->isNotEmpty()) {
                                LaravelNotification::send($superAdmins, new ApprovalDiminta($approval));
                            }
                            Notification::make()->title('Menunggu Approval')->body('Permintaan hapus produk permanen telah dikirim ke Superadmin.')->success()->send();
                            throw new Halt(); 
                        }
                    }),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(), 
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
                Tables\Actions\BulkAction::make('printBarcodes')
                    ->label('Cetak Barcode')
                    ->button()
                    ->icon('heroicon-o-printer')
                    ->action(fn($records) => self::generateBulkBarcode($records))
                    ->color('success'),
                Tables\Actions\BulkAction::make('Reset Stok')
                    ->action(fn($records) => $records->each->update(['stock' => 0]))
                    ->button()
                    ->color('info')
                    ->requiresConfirmation(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('printBarcodes')
                    ->label('Cetak Barcode')
                    ->icon('heroicon-o-printer')
                    ->action(fn() => self::generateBulkBarcode(Product::all()))
                    ->color('success'),
            ]);
    }

    public static function updatePricesAndProfit(Get $get, Set $set): void
    {
        $goldType = $get('gold_type');
        $weight = floatval($get('weight_gram'));

        if (empty($goldType) || empty($weight)) {
            $set('selling_price', 0);
            return;
        }

        $goldPriceToday = GoldPrice::where('jenis_emas', $goldType)
                                    ->whereDate('tanggal', Carbon::today())
                                    ->first();

        $pricePerGram = $goldPriceToday?->harga_per_gram ?? 0;

        $sellingPrice = $weight * $pricePerGram;

        $set('selling_price', $sellingPrice);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
        ];
    }

    protected static function generateBulkBarcode($records)
    {
        $barcodes = [];
        $barcodeGenerator = new DNS1D();

        foreach ($records as $product) {
            if (empty($product->barcode)) continue;
            $barcodes[] = [
                'name' => $product->name,
                'price' => $product->selling_price,
                'barcode' => 'data:image/png;base64,' . $barcodeGenerator->getBarcodePNG($product->barcode, 'C128'),
                'number' => $product->barcode
            ];
        }

        if (empty($barcodes)) {
             Notification::make()->title('Tidak ada barcode untuk dicetak')->warning()->send();
             return;
        }

        $pdf = Pdf::loadView('pdf.barcodes.barcode', compact('barcodes'))->setPaper('a4', 'portrait');
        return response()->streamDownload(fn() => print($pdf->output()), 'barcodes.pdf');
    }
}