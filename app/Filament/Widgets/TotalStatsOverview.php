<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\CashFlow;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Support\Facades\Cache; // <-- Tambahkan ini
use Illuminate\Support\Facades\DB; // <-- Tambahkan ini

class TotalStatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getDescription(): ?string
    {
        return 'Total dari semua perhitungan';
    }

    protected function getHeading(): ?string
    {
        return 'Total Keseluruhan';
    }

    protected function getStats(): array
    {
        // Tentukan kunci cache dan durasi (misal 6 jam)
        $cacheKey = 'total_stats_overview';
        $cacheDuration = now()->addHours(6);

        // Ambil dari cache, atau jalankan query jika cache tidak ada
        $stats = Cache::remember($cacheKey, $cacheDuration, function () {
            
            // Optimasi: Hitung inflow dan outflow dalam 1 query
            $totals = CashFlow::query()
                ->select(
                    DB::raw('SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as total_inflow'),
                    DB::raw('SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as total_outflow')
                )
                ->first();

            $totalInFlow = $totals->total_inflow ?? 0;
            $totalOutFlow = $totals->total_outflow ?? 0;

            return [
                'totalInFlow' => $totalInFlow,
                'totalOutFlow' => $totalOutFlow,
            ];
        });

        // Kembalikan data untuk widget
        return [
            Stat::make('Total Uang Masuk', 'Rp ' . number_format($stats['totalInFlow'], 0, ",", ".")),
            Stat::make('Total Uang Keluar', 'Rp ' . number_format($stats['totalOutFlow'], 0, ",", ".")),
            Stat::make('Total Uang Toko', 'Rp ' . number_format($stats['totalInFlow'] - $stats['totalOutFlow'], 0, ",", ".")),
        ];
    }
}