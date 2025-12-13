<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PaymentMethodPieChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Transaksi per Metode Pembayaran';
    protected static ?string $description = 'Total Jumlah Transaksi per Metode Pembayaran';
    protected static ?int $sort = 5;

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        $cacheKey = 'payment_method_chart_' . $startDate . '_' . $endDate;
        $cacheDuration = now()->addMinutes(60);

        return Cache::remember($cacheKey, $cacheDuration, function () use ($startDate, $endDate) {

            $data = Transaction::query()
                ->when($startDate, fn ($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn ($query) => $query->whereDate('created_at', '<=', $endDate))
                ->join('payment_methods', 'transactions.payment_method_id', '=', 'payment_methods.id')
                ->select('payment_methods.name', DB::raw('COUNT(transactions.id) as total'))
                ->groupBy('payment_methods.name')
                ->get();

            $labels = $data->pluck('name')->toArray();
            $values = $data->pluck('total')->toArray();

            return [
                'datasets' => [
                    [
                        'label' => 'Jumlah Transaksi',
                        'data' => $values,
                        'backgroundColor' => [
                            '#f87171',
                            '#60a5fa',
                            '#34d399',
                            '#facc15',
                            '#c084fc',
                        ],
                        'borderColor' => '#ffffff',
                    ],
                ],
                'labels' => $labels,
            ];
        });
    }


    protected function getType(): string
    {
        return 'doughnut';
    }
}