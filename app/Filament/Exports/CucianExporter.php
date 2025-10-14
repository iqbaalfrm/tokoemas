<?php

namespace App\Filament\Exports;

use App\Models\Cucian;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class CucianExporter extends Exporter
{
    protected static ?string $model = Cucian::class;

    public function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('ID'),
            ExportColumn::make('tanggal'),
            ExportColumn::make('status'),
            ExportColumn::make('berat_total')->label('Berat Total (g)'),
            ExportColumn::make('catatan'),
            ExportColumn::make('created_at')->label('Tanggal Dibuat'),
            ExportColumn::make('updated_at')->label('Tanggal Diperbarui'),
        ];
    }

    public function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Ekspor data cucian Anda telah selesai dan ' . number_format($export->successful_rows) . ' baris telah diekspor.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal diekspor.';
        }

        return $body;
    }
}