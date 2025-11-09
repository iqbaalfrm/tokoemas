<?php

namespace App\Filament\Pages;

use App\Models\Category;
use App\Models\Product;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Grouping\Group;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use App\Exports\LaporanProdukExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanProduk extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static string $view = 'filament.pages.laporan-produk';
    protected static ?string $navigationLabel = 'Laporan Produk';
    protected static ?string $title = 'Laporan Produk';
    protected static ?string $navigationGroup = 'Menejemen keuangan';
    protected static ?int $navigationSort = 6;

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(['super_admin', 'admin']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
              
                Product::query()->with(['subCategory.category'])
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Barang')
                    ->searchable(),
                
                TextColumn::make('sku')
                    ->label('Kode')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('weight_gram')
                    ->label('Berat')
                    ->suffix(' g')
                    ->numeric(3),
            ])
            
 
            ->defaultGroup('subCategory.category.name')
            
            ->groups([

                Group::make('subCategory.category.name')
                    ->label('Kategori')
        
            ])
            
            ->headerActions([
                Action::make('Download Laporan (Excel)')
                    ->label('Download Laporan (Excel)')
                    ->color('success') 
                    ->icon('heroicon-o-document-arrow-down') 
                    ->action(function () {
            
                        $query = $this->getFilteredTableQuery();
                        
                        $fileName = 'laporan-produk-' . now()->format('Y-m-d') . '.xlsx';
                        
                        return Excel::download(
                            new LaporanProdukExport($query), 
                            $fileName
                        );
                    
                    })
            ])
    
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }
}