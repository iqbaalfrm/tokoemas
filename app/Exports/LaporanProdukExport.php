<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Database\Eloquent\Builder;

class LaporanProdukExport implements FromQuery, WithHeadings, WithMapping
{
    protected $query;

    public function __construct(Builder $query)
    {
        // Terima query yang sudah difilter/digroup dari halaman Filament
        $this->query = $query;
    }

    /**
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function query()
    {
        // Gunakan query yang sudah kita kirim
        return $this->query;
    }

    /**
    * @return array
    */
    public function headings(): array
    {
        // Ini adalah judul kolom di file Excel
        return [
            'Kategori',
            'Nama Barang',
            'Kode',
            'Berat (g)',
        ];
    }

    /**
    * @param mixed $product
    * @return array
    */
    public function map($product): array
    {
        // Ini adalah data per baris
        return [
            $product->category?->name ?? 'Tanpa Kategori',
            $product->name,
            $product->sku ?? '-',
            $product->weight_gram,
        ];
    }
}
