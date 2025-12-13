<?php

namespace App\Filament\Resources\CashFlowResource\Widgets;

use App\Filament\Resources\CashFlowResource\Pages\ListCashFlows;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class IncomeOverview extends BaseWidget
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListCashFlows::class;
    }
    
    protected function getStats(): array
    {
        $isSuperAdmin = auth()->user()->hasRole('super_admin');

        if ($isSuperAdmin) {
            $totalCapital = $this->getPageTableQuery()->where('type','income')->where('source','like','%capital%')->sum('amount') ?? 0;
            $totalIncome = $this->getPageTableQuery()->where('type','income')->sum('amount') ?? 0;
            $totalExpense = $this->getPageTableQuery()->where('type','expense')->sum('amount') ?? 0;
            $totalShopMoney = $totalIncome - $totalExpense;

            return [
                Stat::make('Total Modal', 'Rp ' . number_format($totalCapital, 0, ",", "."))
                ->description('Total Capital'),
                Stat::make('Total Uang Masuk', 'Rp ' . number_format($totalIncome, 0, ",", "."))
                ->description('Cash Inflow')
                ->descriptionIcon('heroicon-m-arrow-trending-up',IconPosition::Before)
                ->color('success'),
                Stat::make('Total Uang Keluar', 'Rp ' . number_format($totalExpense, 0, ",", "."))
                ->description('Cash Outflow')
                ->descriptionIcon('heroicon-m-arrow-trending-down',IconPosition::Before)
                ->color('danger'),
                Stat::make('Total Uang Toko', 'Rp ' . number_format($totalShopMoney, 0, ",", "."))
                ->description('masuk : Rp ' . number_format($totalIncome, 0, ",", ".") . ' - Keluar : Rp ' . number_format($totalExpense, 0, ",", ".")),
            ];
        } else {
            return [
                Stat::make('Total Modal', 'Rp ******')
                ->description('Total Capital'),
                Stat::make('Total Uang Masuk', 'Rp ******')
                ->description('Cash Inflow')
                ->descriptionIcon('heroicon-m-arrow-trending-up',IconPosition::Before)
                ->color('success'),
                Stat::make('Total Uang Keluar', 'Rp ******')
                ->description('Cash Outflow')
                ->descriptionIcon('heroicon-m-arrow-trending-down',IconPosition::Before)
                ->color('danger'),
                Stat::make('Total Uang Toko', 'Rp ******')
                ->description('Data terbatas'),
            ];
        }
    }
}
