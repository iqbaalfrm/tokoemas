<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-bold text-gray-800 dark:text-white">Keranjang</h2>
    <button wire:click="resetOrder"
        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
        Reset
    </button>
</div>

@if (empty($order_items))
    <div class="flex flex-col items-center justify-center py-12">
        <img src="{{ asset('images/cart-empty.png') }}" alt="Empty Cart"
            class="w-24 h-24 mb-4 opacity-50">
        <p class="text-gray-500 dark:text-gray-400 font-medium">Keranjang Kosong</p>
        <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Pilih produk untuk memulai</p>
    </div>
@else
    <div class="{{ count($order_items) >= 4 ? 'max-h-[300px] lg:max-h-[400px] overflow-y-auto pr-2' : '' }} space-y-3">
        @foreach ($order_items as $item)
            <div wire:key="{{ $item['product_id'] }}"
                class="bg-gray-50 dark:bg-gray-700 rounded-xl p-3 transition-all duration-200 hover:shadow-md">
                <div class="flex items-center gap-3">
                    <img src="{{ $item['image_url'] ? asset('storage/' . $item['image_url']) : asset('images/default-product.png') }}"
                        alt="{{ $item['name'] }}"
                        class="w-14 h-14 lg:w-16 lg:h-16 object-cover rounded-lg shadow-sm">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white line-clamp-1">
                            {{ $item['name'] }}
                        </h4>
                        
                        <div class="relative mt-1"
                            x-data="{
                                price: @js($item['selling_price']),
                                formatCurrency(value) {
                                    if (!value) return '';
                                    return parseInt(value).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                                }
                            }"
                            x-init="$watch('price', value => $wire.set('order_items.{{ $item['product_id'] }}.selling_price', value, true))"
                        >
                            <span class="absolute left-0 top-1/2 -translate-y-1/2 text-sm text-gray-500 dark:text-gray-400">Rp</span>
                            <input type="text"
                                x-model="price"
                                x-on:input.debounce.500ms="price = $event.target.value.replace(/\./g, '')"
                                x-bind:value="formatCurrency(price)"
                                x-init="$el.value = formatCurrency(price)"
                                class="w-full bg-transparent border-0 border-b-2 border-gray-300 dark:border-gray-600 pl-6 p-1 text-sm font-semibold text-gray-600 dark:text-white focus:ring-0 focus:border-green-500 transition">
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between mt-3">
                    <span class="text-xs lg:text-sm text-gray-500 dark:text-gray-400">
                        Subtotal: Rp {{ number_format($item['selling_price'] * $item['quantity'], 0, ',', '.') }}
                    </span>
                    <div class="flex items-center gap-2">
                        <button wire:click="decreaseQuantity({{ $item['product_id'] }})"
                            class="w-8 h-8 rounded-lg bg-gray-200 dark:bg-gray-600 hover:bg-white dark:hover:bg-gray-500 text-gray-700 dark:text-white font-medium transition-colors duration-200">
                            -
                        </button>
                        <span class="w-8 lg:w-10 text-center font-semibold text-gray-800 dark:text-white">
                            {{ $item['quantity'] }}
                        </span>
                        <button wire:click="increaseQuantity({{ $item['product_id'] }})"
                            class="w-8 h-8 rounded-lg bg-green-500 hover:bg-green-600 text-white font-medium transition-colors duration-200">
                            +
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6 pt-6 border-t-2 border-gray-200 dark:border-gray-700">
        <div class="flex justify-between items-center mb-4">
            <span class="text-lg font-medium text-gray-600 dark:text-gray-400">Total</span>
            <span class="text-xl lg:text-2xl font-bold text-gray-800 dark:text-white">
                Rp {{ number_format($this->calculateTotal(), 0, ',', '.') }}
            </span>
        </div>
    </div>
@endif

<button type="button" wire:click="$set('showCheckoutModal', true)"
    class="w-full mt-4 py-3 lg:py-4 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 {{ empty($order_items) ? 'opacity-50 cursor-not-allowed' : '' }}"
    {{ empty($order_items) ? 'disabled' : '' }}>
    <div class="flex items-center justify-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
            </path>
        </svg>
        Checkout
    </div>
</button>
