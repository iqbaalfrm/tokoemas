<?php

namespace App\Filament\Widgets;

use Filament\Forms;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CashFlowRadarChart extends ChartWidget
{
    protected static ?string $heading = 'Arus Kas';
    protected static ?string $description = 'Total Jumlah Pergerakan Alur Kas';
    protected static ?int $sort = 6;

    protected function getData(): array
    {
        // Check if current user is super admin
        $isSuperAdmin = auth()->user()?->hasRole('super_admin') ?? false;

        $cacheKey = 'cash_flow_chart_widget';
        $cacheDuration = now()->addMinutes(60);

        return Cache::remember($cacheKey, $cacheDuration, function () use ($isSuperAdmin) {

            if (!$isSuperAdmin) {
                // Return empty data for non-super-admin users
                return [
                    'labels' => ['Data Terbatas'],
                    'datasets' => [
                        [
                            'label' => 'Net Cash Flow per Source',
                            'data' => [0],
                            'backgroundColor' => ['rgba(200, 200, 200, 0.5)'],
                            'borderColor' => 'rgba(0, 0, 0, 0.1)',
                        ],
                    ],
                ];
            }

            $data = DB::table('cash_flows')
                ->select(
                    'source',
                    DB::raw('SUM(CASE WHEN type = "income" THEN amount ELSE -amount END) as net_total')
                )
                ->groupBy('source')
                ->get();

            return [
                'labels' => $data->pluck('source'),
                'datasets' => [
                    [
                        'label' => 'Net Cash Flow per Source',
                        'data' => $data->pluck('net_total'),
                        'backgroundColor' => [
                            'rgb(52, 211, 153)',
                            'rgb(251, 191, 36)',
                            'rgb(239, 68, 68)',
                            'rgb(96, 165, 250)',
                            'rgb(168, 85, 247)',
                            'rgb(236, 72, 153)',
                            'rgb(34, 197, 94)',
                            'rgb(253, 224, 71)',
                            'rgb(250, 204, 21)',
                            'rgb(29, 78, 216)',
                            'rgb(124, 58, 237)',
                            'rgb(236, 72, 153)',
                            'rgb(16, 185, 129)',
                            'rgb(59, 130, 246)',
                            'rgb(232, 62, 140)',

                        ],
                        'borderColor' => 'rgba(0, 0, 0, 0.1)',
                    ],
                ],
            ];
        });
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}