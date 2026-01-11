<?php

namespace App\Filament\Exports;

use App\Models\Product;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ProductExporter extends Exporter
{
    protected static ?string $model = Product::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('category.name')
                ->label('Kategori'),
            ExportColumn::make('name')
                ->label('Nama Barang'),
            ExportColumn::make('sku')
                ->label('Kode'),
            ExportColumn::make('weight_gram')
                ->label('Berat (g)'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Laporan produk selesai diekspor ' . number_format($export->successful_rows) . ' ' . str('baris')->plural($export->successful_rows) . ' diekspor.';

        if ($failedRowsCount = $export->failed_rows) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('baris')->plural($failedRowsCount) . ' gagal diekspor.';
        }

        return $body;
    }

    public function getQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getQuery()->with('category')->orderBy('category_id');
    }
}