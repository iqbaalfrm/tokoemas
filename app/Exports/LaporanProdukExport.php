<?php

namespace App\Exports;

use App\Models\BuybackItem;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Database\Eloquent\Builder;

class LaporanProdukExport implements FromQuery, WithHeadings, WithMapping
{
    protected $query;

    public function __construct(Builder $query)
    {
        // Terima query yang sudah difilter dari halaman Filament
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
            'Nama Pembeli',
            'Nama Barang',
            'Harga',
        ];
    }

    /**
    * @param mixed $buybackItem
    * @return array
    */
    public function map($buybackItem): array
    {
        // Ini adalah data per baris
        return [
            $buybackItem->buyback->customer_name ?? '-',
            $buybackItem->nama_produk,
            $buybackItem->item_total_price,
        ];
    }
}
