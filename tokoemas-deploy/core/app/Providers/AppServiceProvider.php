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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use App\Observers\InventoryItemObserver;
use App\Observers\TransactionItemObserver;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Illuminate\Support\Facades\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register TenantService sebagai singleton
        $this->app->singleton(\App\Services\TenantService::class, function ($app) {
            return new \App\Services\TenantService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // FORCE HTTPS (Wajib di Hosting Production)
        if (app()->environment('production') || config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        Model::shouldBeStrict(! app()->isProduction());

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

        // --- SOLUSI ERROR 419 LIVEWIRE/FILAMENT (MULTI-DOMAIN) ---
        // Memaksa Livewire menggunakan URL update yang benar sesuai domain yang diakses
        if (!app()->runningInConsole()) {
            Livewire::setUpdateRoute(function ($handle) {
                return Route::post('/livewire/update', $handle)
                    ->middleware(['web']);
            });
        }

        // --- IBNU LOGIC (READ-ONLY MIRROR) ---
        // Check host. Note: In console/migrating, request might not work as expected, ensure safe check.
        if (!app()->runningInConsole() && request()->getHost() && str_contains(request()->getHost(), 'tokoemasibnu.my.id')) {
            
            // 1. Force View Trashed (Transparan terhadap Soft Delete di Hartowiyono)
            $modelsToWatch = [Product::class, Transaction::class, Inventory::class, InventoryItem::class, TransactionItem::class];
            
            foreach ($modelsToWatch as $model) {
                if (method_exists($model, 'bootSoftDeletes')) {
                    $model::addGlobalScope('ibnu_trashed', function (Builder $builder) {
                        $builder->withTrashed();
                    });
                }
            }

            // 2. Disable Deletion (Strict)
            Gate::before(function ($user, $ability) {
                if (in_array($ability, ['delete', 'deleteAny', 'forceDelete', 'forceDeleteAny', 'restore'])) {
                    return false;
                }
            });
        }
    }
}
