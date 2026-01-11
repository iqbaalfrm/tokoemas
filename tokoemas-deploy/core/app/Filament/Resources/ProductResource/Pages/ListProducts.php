<?php

namespace App\Filament\Resources\ProductResource\Pages;

use Filament\Actions;
use App\Models\Product;
use Filament\Actions\Action;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Resources\Components\Tab;
use Illuminate\Support\Facades\Session;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProductResource;


class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function setFlashMessage()
    {
        $error = Session::get('error');

        if ($error) {
            $this->notify($error, 'danger');
            Session::forget('error');
        }
    }

    public function getTabs(): array
{
    // Optimized tab counts using cached values to avoid multiple count queries
    $counts = cache()->remember('product_tab_counts', 300, function () { // Cache for 5 minutes
        return [
            'stock_banyak' => Product::query()->where('stock', '>=', 10)->count(),
            'stock_sedikit' => Product::query()->where('stock', '<', 10)->where('stock', '>', 0)->count(),
            'stock_habis' => Product::query()->where('stock', '<=', 0)->count(),
        ];
    });

    return [
        'all' => Tab::make(),
        'Stock Banyak' => Tab::make()
        ->modifyQueryUsing(fn (Builder $query) => $query->where('stock', '>', 10))
        ->badge($counts['stock_banyak'])
        ->badgeColor('success'),
        'Stock Sedikit' => Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where( 'stock', '<', 10 ,)->where('stock', '>', 0))
            ->badge($counts['stock_sedikit'])
            ->badgeColor('warning'),
        'Stock Habis' => Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where('stock', '=', 0))
            ->badge($counts['stock_habis'])
            ->badgeColor('danger'),
    ];
}
}