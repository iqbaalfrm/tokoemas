<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\GoldPrice;
use App\Models\Product;
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
// --- TAMBAHAN USE STATEMENTS ---
use App\Models\Approval;
use App\Models\User;
use App\Notifications\ApprovalDiminta;
use Filament\Notifications\Notification; // Pastikan ini Notification Filament
use Illuminate\Database\Eloquent\Model; // Tambahkan jika belum ada
use Illuminate\Support\Facades\Notification as LaravelNotification; // Alias untuk notif Laravel
use Filament\Support\Exceptions\Halt; // <-- TAMBAHAN PENTING
// --- AKHIR TAMBAHAN ---

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
                Forms\Components\TextInput::make('name')
                    ->label('Nama Produk')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('category_id')
                    ->label('Kategori Produk')
                    ->relationship('category', 'name')
                    ->required(),

                Forms\Components\Select::make('gold_type')
                    ->label('Jenis Emas')
                    ->options([
                        'Emas Tua' => 'Emas Tua',
                        'Emas Muda' => 'Emas Muda',
                    ])
                    ->live()
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::updatePricesAndProfit($get, $set))
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
                    ->label('SKU')
                    ->helperText('Jika tidak diisi akan di generate otomatis')
                    ->maxLength(255),

                Forms\Components\TextInput::make('barcode')
                    ->label('Kode Barcode')
                    ->numeric()
                    ->helperText('Jika tidak diisi akan di generate otomatis')
                    ->maxLength(255),

                Forms\Components\Toggle::make('is_active')
                    ->label('Produk Aktif')
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
                    ->description(fn(Product $record): string => $record->category()->withTrashed()->value('name'))
                    ->searchable(),
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
                Tables\Columns\BooleanColumn::make('is_active')
                    ->label('Produk Aktif'),
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
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->options(Category::all()->pluck('name', 'id'))
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\Action::make('Reset Stok')
                    ->action(fn(Product $record) => $record->update(['stock' => 0]))
                    ->button()
                    ->color('info')
                    ->requiresConfirmation(),
                Tables\Actions\EditAction::make()->button(),

                // --- DELETE ACTION (SOFT DELETE) DENGAN APPROVAL ---
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
                                'changes' => ['action' => 'delete'], // Tanda request SOFT DELETE
                                'status' => 'pending',
                            ]);

                            $superAdmins = User::role('super_admin')->get();
                            if ($superAdmins->isNotEmpty()) {
                                LaravelNotification::send($superAdmins, new ApprovalDiminta($approval));
                            }

                            Notification::make()->title('Menunggu Approval')->body('Permintaan hapus produk (sementara) telah dikirim ke Superadmin.')->success()->send();
                            throw new Halt(); // Hentikan aksi soft delete default
                        }
                    }),
                // --- AKHIR PERUBAHAN DELETE ACTION ---

                // --- FORCE DELETE ACTION DENGAN APPROVAL ---
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
                            throw new Halt(); // Hentikan aksi force delete default
                        }
                    }),
                // --- AKHIR PERUBAHAN FORCE DELETE ACTION ---
                    
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