<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BuybackResource\Pages;
use App\Models\Buyback;
use App\Models\Product; 
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BuybackResource extends Resource
{
    protected static ?string $model = Buyback::class;

    protected static ?string $slug = 'buyback';
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
                        Forms\Components\TextInput::make('customer_phone')
                            ->label('Nomer HP')
                            ->required()
                            ->tel()
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('customer_name')
                            ->label('Nama Pembeli')
                            ->required()
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('customer_address')
                            ->label('Alamat')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('ktp_image')
                            ->label('Foto KTP')
                            ->image()
                            ->directory('ktp-buyback')
                            ->maxSize(10240) // 10MB
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg'])
                            ->columnSpanFull(),
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
                        Forms\Components\TextInput::make('total_amount_paid')
                            ->label('Total Pembayaran')
                            ->numeric()
                            ->readOnly()
                            ->prefix('Rp')
                            ->default(0),
                    ])->columns(4),

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
                                Forms\Components\TextInput::make('item_total_price')
                                    ->label('Harga Beli')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->default(0),
                                Forms\Components\FileUpload::make('foto')
                                    ->disk('public')
                                    ->directory('buyback-photos')
                                    ->image()
                                    ->imageEditor()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/webp'])
                                    ->maxSize(10240) // 10MB
                                    ->columnSpanFull(),
                            ])
                            ->columns(4)
                            ->addActionLabel('Tambahkan item buyback')
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $items = $get('buybackItems');
                                $totalWeight = 0;
                                $totalPrice = 0;
                                if (is_array($items)) {
                                    foreach ($items as $item) {
                                        if (!empty($item['berat']) && is_numeric($item['berat'])) {
                                            $totalWeight += floatval($item['berat']);
                                        }
                                        if (!empty($item['item_total_price']) && is_numeric($item['item_total_price'])) {
                                            $totalPrice += floatval($item['item_total_price']);
                                        }
                                    }
                                }
                                $set('berat_total', $totalWeight);
                                $set('total_amount_paid', $totalPrice);
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
            ->modifyQueryUsing(fn ($query) => $query->with(['user', 'approver']))
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')->date('d M Y')->sortable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Nama Pembeli')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('tipe')
                    ->colors([
                        'info' => 'pelanggan',
                        'success' => 'pembelian_stok',
                    ])
                    ->formatStateUsing(fn (string $state): string => Str::title(str_replace('_', ' ', $state)))
                    ->searchable(),
                Tables\Columns\TextColumn::make('berat_total')->suffix(' g')->sortable(),
                Tables\Columns\TextColumn::make('total_amount_paid')
                    ->label('Total Pembayaran')
                    ->money('IDR', true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('approver.name')
                    ->label('Disetujui Oleh')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('approved_at')
                    ->dateTime('d M Y H:i')
                    ->label('Waktu Approval')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Dibuat Oleh')
                    ->searchable()
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        // Ensure the relationship is loaded and return the user name
                        return $record->user ? $record->user->name : 'System';
                    }),
                Tables\Columns\TextColumn::make('buyback_items_count')->counts('buybackItems')->label('Jumlah Item'),
            ])
            ->filters([
                Tables\Filters\Filter::make('tanggal')
                    ->form([
                        Forms\Components\DatePicker::make('tanggal_from')
                            ->label('Tanggal Awal')
                            ->placeholder('Pilih tanggal awal'),
                        Forms\Components\DatePicker::make('tanggal_to')
                            ->label('Tanggal Akhir')
                            ->placeholder('Pilih tanggal akhir'),
                    ])
                    ->query(function ($query, array $data): \Illuminate\Database\Eloquent\Builder {
                        return $query
                            ->when(
                                $data['tanggal_from'],
                                fn ($query, $date): \Illuminate\Database\Eloquent\Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['tanggal_to'],
                                fn ($query, $date): \Illuminate\Database\Eloquent\Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['tanggal_from'] ?? null) {
                            $indicators['tanggal_from'] = 'Tanggal dari ' . \Carbon\Carbon::parse($data['tanggal_from'])->format('d/m/Y');
                        }
                        if ($data['tanggal_to'] ?? null) {
                            $indicators['tanggal_to'] = 'Tanggal hingga ' . \Carbon\Carbon::parse($data['tanggal_to'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('history')
                        ->icon('heroicon-o-clock')
                        ->label('Riwayat')
                        ->modalHeading('Riwayat Proses')
                        ->modalContent(fn ($record) => view('filament.components.timeline', ['logs' => $record->logs->sortByDesc('created_at')]))
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Tutup'),
                    Action::make('Lihat Foto')
                        ->icon('heroicon-o-photo')
                        ->color('gray')
                        ->modalContent(fn (Buyback $record): \Illuminate\View\View => view(
                            'filament.modals.view-buyback-photos',
                            ['items' => $record->buybackItems()->whereNotNull('foto')->get()]
                        ))
                        ->modalSubmitAction(false)
                        ->modalCancelAction(false)
                        ->modalWidth('2xl'),
                    Action::make('view_ktp')
                        ->label('Lihat KTP')
                        ->icon('heroicon-o-identification')
                        ->color('info')
                        ->visible(fn ($record) => !empty($record->ktp_image))
                        ->modalHeading('Foto KTP Pelanggan')
                        ->modalSubmitAction(false)
                        ->modalCancelAction(fn ($action) => $action->label('Tutup'))
                        ->modalContent(fn ($record) => new \Illuminate\Support\HtmlString('
                            <div class="flex justify-center">
                                <img src="'.asset('storage/'.$record->ktp_image).'" style="max-width: 100%; max-height: 500px; border-radius: 8px;">
                            </div>
                        ')),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
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

