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
        $filter = $this->filters['range'] ?? 'today';
        $startDateFilter = $this->filters['startDate'] ?? null;
        $endDateFilter = $this->filters['endDate'] ?? null;

        $cacheKey = 'stats_overview_' . $filter . '_' . $startDateFilter . '_' . $endDateFilter;
        $cacheDuration = now()->addMinutes(60);

        return Cache::remember($cacheKey, $cacheDuration, function () use ($filter, $startDateFilter, $endDateFilter) {

            // Check if current user is super admin
            $isSuperAdmin = auth()->check() && auth()->user()->hasRole('super_admin');

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

            $transactionQuery = Transaction::whereBetween('created_at', [$startDate, $endDate]);

            $omset = $transactionQuery->sum('total');
            $omsetChart = $transactionQuery->pluck('total')->toArray();

            $inFlowQuery = CashFlow::where('type', 'income')->whereBetween('created_at', [$startDate, $endDate]);
            $outFlowQuery = CashFlow::where('type', 'expense')->whereBetween('created_at', [$startDate, $endDate]);

            $inFlow = $inFlowQuery->clone()->sum('amount');
            $inFlowChart = $inFlowQuery->clone()->pluck('amount')->toArray();

            $outFlow = $outFlowQuery->clone()->sum('amount');
            $outFlowChart = $outFlowQuery->clone()->pluck('amount')->toArray();

            // Format the values based on user role
            $omsetValue = $isSuperAdmin ? 'Rp ' . number_format($omset, 0, ",", ".") : 'Rp ******';
            $inFlowValue = $isSuperAdmin ? 'Rp ' . number_format($inFlow, 0, ",", ".") : 'Rp ******';
            $outFlowValue = $isSuperAdmin ? 'Rp ' . number_format($outFlow, 0, ",", ".") : 'Rp ******';

            // Format the chart data based on user role (protect chart values too)
            $omsetChartForDisplay = $isSuperAdmin ? $omsetChart : array_fill(0, count($omsetChart), 0);
            $inFlowChartForDisplay = $isSuperAdmin ? $inFlowChart : array_fill(0, count($inFlowChart), 0);
            $outFlowChartForDisplay = $isSuperAdmin ? $outFlowChart : array_fill(0, count($outFlowChart), 0);

            // Format the descriptions based on user role
            $omsetDesc = $isSuperAdmin ? 'Total Penjualan' : '******';
            $inFlowDesc = $isSuperAdmin ? 'Cash Inflow' : '******';
            $outFlowDesc = $isSuperAdmin ? 'Cash Outflow' : '******';

            return [
                Stat::make('Penjualan', $omsetValue)
                    ->description($omsetDesc)
                    ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                    ->chart($omsetChartForDisplay)
                    ->color('success'),
                Stat::make('Uang Masuk', $inFlowValue)
                    ->description($inFlowDesc)
                    ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                    ->chart($inFlowChartForDisplay)
                    ->color('success'),
                Stat::make('Uang Keluar', $outFlowValue)
                    ->description($outFlowDesc)
                    ->descriptionIcon('heroicon-m-arrow-trending-down', IconPosition::Before)
                    ->chart($outFlowChartForDisplay)
                    ->color('danger'),
            ];

        });
    }
}