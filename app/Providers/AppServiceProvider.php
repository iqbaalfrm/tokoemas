<?php

namespace App\Providers;

use App\Models\Report;
use App\Models\Product;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Transaction;
use App\Models\InventoryItem;
use App\Models\TransactionItem;
use App\Models\Member;
use App\Models\PaymentMethod;
use App\Models\Cucian;
use App\Models\Buyback;
use Filament\Support\Assets\Js;
use App\Observers\ReportObserver;
use App\Observers\ProductObserver;
use App\Observers\CategoryObserver;
use App\Observers\InventoryObserver;
use App\Observers\TransactionObserver;
use App\Observers\MemberObserver;
use App\Observers\PaymentMethodObserver;
use App\Observers\CucianObserver;
use App\Observers\BuybackObserver;
use Illuminate\Support\ServiceProvider;
use App\Observers\InventoryItemObserver;
use App\Observers\TransactionItemObserver;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Inventory::observe(InventoryObserver::class);
        InventoryItem::observe(InventoryItemObserver::class);
        TransactionItem::observe(TransactionItemObserver::class);
        Transaction::observe(TransactionObserver::class);
        Category::observe(CategoryObserver::class);
        Product::observe(ProductObserver::class);
        Report::observe(ReportObserver::class);
        Member::observe(MemberObserver::class);
        PaymentMethod::observe(PaymentMethodObserver::class);
        Cucian::observe(CucianObserver::class);
        Buyback::observe(BuybackObserver::class);

        FilamentAsset::register([
            Js::make('printer-thermal', asset('js/printer-thermal.js'))
        ]);

    }
}
