<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\CashFlow;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
        // Check if current user is super admin
        $isSuperAdmin = auth()->user()?->hasRole('super_admin') ?? false;

        $cacheKey = 'total_stats_overview';
        $cacheDuration = now()->addHours(6);

        $stats = Cache::remember($cacheKey, $cacheDuration, function () {

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

        // Format the values based on user role
        $totalInFlowValue = $isSuperAdmin ? 'Rp ' . number_format($stats['totalInFlow'], 0, ",", ".") : 'Rp ******';
        $totalOutFlowValue = $isSuperAdmin ? 'Rp ' . number_format($stats['totalOutFlow'], 0, ",", ".") : 'Rp ******';
        $totalStoreValue = $isSuperAdmin ? 'Rp ' . number_format($stats['totalInFlow'] - $stats['totalOutFlow'], 0, ",", ".") : 'Rp ******';

        return [
            Stat::make('Total Uang Masuk', $totalInFlowValue),
            Stat::make('Total Uang Keluar', $totalOutFlowValue),
            Stat::make('Total Uang Toko', $totalStoreValue),
        ];
    }
}