<?php

namespace App\Filament\Resources;

use App\Filament\Exports\CucianExporter;
use App\Filament\Resources\CucianResource\Pages;
use App\Models\Cucian;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CucianResource extends Resource
{
    protected static ?string $model = Cucian::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationLabel = 'Cucian';
    protected static ?string $modelLabel = 'Cucian';
    protected static ?string $navigationGroup = 'Menejemen Produk';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Cucian')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal')
                            ->required()
                            ->default(now()),
                        Forms\Components\ToggleButtons::make('status')
                            ->required()
                            ->inline()
                            ->options([
                                'Proses' => 'Proses',
                                'Selesai' => 'Selesai',
                            ])
                            ->colors([
                                'Proses' => 'warning',
                                'Selesai' => 'success',
                            ])
                            ->default('Proses'),
                        Forms\Components\TextInput::make('berat_total')
                            ->label('Berat Total (g)')
                            ->numeric()
                            ->readOnly()
                            ->default(0),
                    ])->columns(2),

                Forms\Components\Section::make('Barang yang Dicuci')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->label('Cucian Items')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('nama_produk')
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('berat')
                                    ->numeric()
                                    ->required()
                                    ->suffix('g'),
                            ])
                            ->columns(3)
                            ->addActionLabel('Tambahkan item cucian')
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $items = $get('items');
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
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'Proses',
                        'success' => 'Selesai',
                    ])
                    ->searchable(),
                Tables\Columns\TextColumn::make('berat_total')->suffix(' g')->sortable(),
                Tables\Columns\TextColumn::make('items_count')->counts('items')->label('Jumlah Item'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->label('Ekspor ke Excel')
                    ->exporter(CucianExporter::class)
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
            'index' => Pages\ListCucians::route('/'),
            'create' => Pages\CreateCucian::route('/create'),
            'edit' => Pages\EditCucian::route('/{record}/edit'),
        ];
    }    
}