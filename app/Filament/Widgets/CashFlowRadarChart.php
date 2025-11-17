<?php

namespace App\Filament\Widgets;

use Filament\Forms;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache; // <-- Tambahkan ini

class CashFlowRadarChart extends ChartWidget
{
    protected static ?string $heading = 'Arus Kas';
    protected static ?string $description = 'Total Jumlah Pergerakan Alur Kas';
    protected static ?int $sort = 6;

    protected function getData(): array
    {
        // Tentukan kunci cache dan durasi (misal 60 menit)
        $cacheKey = 'cash_flow_chart_widget';
        $cacheDuration = now()->addMinutes(60);

        // Gunakan Cache::remember untuk mengambil/menyimpan data
        return Cache::remember($cacheKey, $cacheDuration, function () {
            
            // Query yang sudah dioptimasi
            $data = DB::table('cash_flows')
                ->select(
                    'source',
                    DB::raw('SUM(CASE WHEN type = "income" THEN amount ELSE -amount END) as net_total')
                )
                ->groupBy('source')
                ->get();

            // Format data untuk chart
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