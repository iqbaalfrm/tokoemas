<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\CashFlow;
use App\Models\Transaction;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Support\Facades\Cache; 

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        // Ambil filter tanggal dari state
        $filter = $this->filters['range'] ?? 'today';
        $startDateFilter = $this->filters['startDate'] ?? null;
        $endDateFilter = $this->filters['endDate'] ?? null;

        // Buat kunci cache yang unik berdasarkan filter
        $cacheKey = 'stats_overview_' . $filter . '_' . $startDateFilter . '_' . $endDateFilter;
        $cacheDuration = now()->addMinutes(60); // Simpan 1 jam

        // Ambil dari cache, atau jalankan query jika cache tidak ada
        return Cache::remember($cacheKey, $cacheDuration, function () use ($filter, $startDateFilter, $endDateFilter) {

            // 1. Logika untuk menentukan rentang tanggal
            if ($filter === 'custom') {
                $startDate = !is_null($startDateFilter)
                    ? Carbon::parse($startDateFilter) : now()->startOfDay();
                $endDate = !is_null($endDateFilter)
                    ? Carbon::parse($endDateFilter)->endOfDay() : now()->endOfDay();
            } else {
                [$startDate, $endDate] = match ($filter) {
                    'today' => [now()->startOfDay(), now()->endOfDay()],
                    'this_week' => [now()->startOfWeek(), now()->endOfWeek()],
                    'this_month' => [now()->startOfMonth(), now()->endOfMonth()],
                    'this_year' => [now()->startOfYear(), now()->endOfYear()],
                    default => [now()->startOfDay(), now()->endOfDay()],
                };
            }

            // 2. Siapkan query dasar (belum dieksekusi)
            $transactionQuery = Transaction::whereBetween('created_at', [$startDate, $endDate]);
            $inFlowQuery = CashFlow::where('type', 'income')->whereBetween('created_at', [$startDate, $endDate]);
            $outFlowQuery = CashFlow::where('type', 'expense')->whereBetween('created_at', [$startDate, $endDate]);

            // 3. Eksekusi query optimasi (hanya ambil SUM dan data chart)
            $omset = $transactionQuery->clone()->sum('total');
            $omsetChart = $transactionQuery->clone()->pluck('total')->toArray();
            
            $inFlow = $inFlowQuery->clone()->sum('amount');
            $inFlowChart = $inFlowQuery->clone()->pluck('amount')->toArray();
            
            $outFlow = $outFlowQuery->clone()->sum('amount');
            $outFlowChart = $outFlowQuery->clone()->pluck('amount')->toArray();
            
            // 4. Kembalikan data untuk widget
            return [
                Stat::make('Penjualan', 'Rp ' . number_format($omset, 0, ",", "."))
                    ->description('Omset')
                    ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                    ->chart($omsetChart)
                    ->color('success'),
                Stat::make('Uang Masuk', 'Rp ' . number_format($inFlow, 0, ",", "."))
                    ->description('Cash Inflow')
                    ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                    ->chart($inFlowChart)
                    ->color('success'),
                Stat::make('Uang Keluar', 'Rp ' . number_format($outFlow, 0, ",", "."))
                    ->description('Cash Outflow')
                    ->descriptionIcon('heroicon-m-arrow-trending-down', IconPosition::Before)
                    ->chart($outFlowChart)
                    ->color('danger'),
            ];
            
        });
    }
}