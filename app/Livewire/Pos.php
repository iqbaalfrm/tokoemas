<?php

namespace App\Livewire;

use App\Helpers\TransactionHelper;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Member;
use App\Models\User;
use App\Notifications\TransaksiBaruDibuat;
use App\Services\DirectPrintService;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Notification as LaravelNotification;
use Livewire\Component;
use Livewire\WithPagination;

class Pos extends Component
{
    use WithPagination;

    public int|string $perPage = 10;
    public $categories;
    public $selectedCategory;
    public $search = '';
    public $print_via_bluetooth = false;
    public $barcode = '';
    public $name = 'Umum';
    public $payment_method_id;
    public $payment_methods;
    public array $order_items = [];
    public $total_price = 0;
    public $cash_received = '';
    public $change = 0;
    public $showConfirmationModal = false;
    public $showCheckoutModal = false;
    public $orderToPrint = null;
    public $is_cash = true;
    public $selected_payment_method = null;
    public $no_hp;
    public $alamat;
    public $member_id = null;

    protected $listeners = ['scanResult' => 'handleScanResult'];

    public function mount()
    {
        $settings = Setting::first();
        $this->print_via_bluetooth = $settings->print_via_bluetooth ?? false;
        $this->categories = collect([['id' => null, 'name' => 'Semua']])->merge(Category::all());
        if (session()->has('orderItems')) {
            $this->order_items = session('orderItems');
            $this->calculateTotal();
        }
        $this->payment_methods = PaymentMethod::all();
    }

    public function updatedNoHp($value)
    {
        $member = Member::where('no_hp', $value)->first();

        if ($member) {
            $this->member_id = $member->id;
            $this->name = $member->nama;
            $this->alamat = $member->alamat;
            Notification::make()
                ->title('Member ditemukan: ' . $member->nama)
                ->success()
                ->send();
        } else {
            $this->member_id = null;
            Notification::make()
                ->title('Member belum terdaftar — silakan isi data baru')
                ->warning()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.pos', [
            'products' => Product::with(['subCategory.category']) // <-- PERBAIKAN QUERY DI SINI
                ->where('stock', '>', 0)
                ->where('is_active', 1)
                ->when($this->selectedCategory, function (Builder $query) {
                    $query->whereHas('subCategory', function (Builder $subQuery) {
                        $subQuery->where('category_id', $this->selectedCategory);
                    });
                })
                ->where(function ($query) {
                    $query->where('name', 'LIKE', '%' . $this->search . '%')
                        ->orWhere('sku', 'LIKE', '%' . $this->search . '%');
                })
                ->paginate($this->perPage),
        ]);
    }

    public function updatedOrderItems()
    {
        $this->syncCart();
    }

    public function updatedPaymentMethodId($value)
    {
        if ($value) {
            $paymentMethod = PaymentMethod::find($value);
            $this->selected_payment_method = $paymentMethod;
            $this->is_cash = $paymentMethod->is_cash ?? false;
            if (!$this->is_cash) {
                $this->cash_received = number_format($this->total_price, 0, ',', '.');
                $this->change = 0;
            } else {
                $this->calculateChange();
            }
        }
    }

    public function updatedCashReceived($value)
    {
        if ($this->is_cash) {
            $this->cash_received = $value;
            $this->calculateChange();
        }
    }

    public function calculateChange()
    {
        $cashReceived = $this->getCashReceivedNumeric();
        $this->change = ($cashReceived >= $this->total_price)
            ? $cashReceived - $this->total_price
            : 0;
    }

    public function getCashReceivedNumeric()
    {
        return floatval(str_replace('.', '', $this->cash_received));
    }

    public function updatedBarcode($barcode)
    {
        if (empty($barcode)) {
            return;
        }
        $product = Product::where('barcode', $barcode)->where('is_active', true)->first();
        if ($product) {
            $this->addToOrder($product->id);
        } else {
            Notification::make()->title('Produk tidak ditemukan ' . $barcode)->danger()->send();
        }
        $this->barcode = '';
    }

    public function handleScanResult($decodedText)
    {
        $this->updatedBarcode($decodedText);
    }

    public function setCategory($categoryId = null)
    {
        $this->selectedCategory = $categoryId;
        $this->resetPage();
    }

    public function addToOrder($productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return;
        }

        if (isset($this->order_items[$productId])) {
            if ($this->order_items[$productId]['quantity'] >= $product->stock) {
                Notification::make()->title('Stok barang tidak mencukupi')->danger()->send();
                return;
            }
            $this->order_items[$productId]['quantity']++;
        } else {
            $this->order_items[$productId] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'selling_price' => $product->selling_price,
                'cost_price' => $product->cost_price,
                'image_url' => $product->image,
                'quantity' => 1,
                'berat' => $product->weight_gram,
            ];
        }
        $this->syncCart();
    }

    public function increaseQuantity($productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return;
        }

        if (isset($this->order_items[$productId])) {
            if ($this->order_items[$productId]['quantity'] + 1 <= $product->stock) {
                $this->order_items[$productId]['quantity']++;
            } else {
                Notification::make()->title('Stok barang tidak mencukupi')->danger()->send();
            }
        }
        $this->syncCart();
    }

    public function decreaseQuantity($productId)
    {
        if (isset($this->order_items[$productId])) {
            if ($this->order_items[$productId]['quantity'] > 1) {
                $this->order_items[$productId]['quantity']--;
            } else {
                unset($this->order_items[$productId]);
            }
        }
        $this->syncCart();
    }

    private function syncCart()
    {
        session()->put('orderItems', $this->order_items);
        $this->calculateTotal();
        if ($this->is_cash && !empty($this->cash_received)) {
            $this->calculateChange();
        }
    }

    public function calculateTotal()
    {
        $total = 0;
        foreach ($this->order_items as $item) {
            $total += ($item['quantity'] ?? 0) * ($item['selling_price'] ?? 0);
        }
        $this->total_price = $total;
        return $total;
    }

    public function resetOrder()
    {
        session()->forget('orderItems');
        $this->reset([
            'order_items', 'payment_method_id', 'total_price', 'cash_received',
            'change', 'is_cash', 'selected_payment_method', 'name', 'no_hp', 'alamat', 'member_id'
        ]);
        $this->name = 'Umum';
    }

    public function checkout()
    {
        $this->validate([
            'payment_method_id' => 'required',
            'name' => 'string|max:255',
        ], [
            'payment_method_id.required' => 'Metode pembayaran harus dipilih',
        ]);

        if ($this->is_cash) {
            if (empty($this->cash_received)) {
                $this->addError('cash_received', 'Nominal bayar harus diisi');
                return;
            }
            if ($this->getCashReceivedNumeric() < $this->total_price) {
                $this->addError('cash_received', 'Nominal bayar kurang dari total belanja');
                return;
            }
        }

        if (empty($this->order_items)) {
            Notification::make()->title('Keranjang kosong')->danger()->send();
            $this->showCheckoutModal = false;
            return;
        }

        if (!$this->member_id && !empty($this->no_hp)) {
            $existingMember = Member::where('no_hp', $this->no_hp)->first();
            if ($existingMember) {
                $this->member_id = $existingMember->id;
            } else {
                $newMember = Member::create([
                    'nama' => $this->name,
                    'no_hp' => $this->no_hp,
                    'alamat' => $this->alamat,
                ]);
                $this->member_id = $newMember->id;
            }
        }

        $order = Transaction::create([
            'member_id' => $this->member_id,
            'payment_method_id' => $this->payment_method_id,
            'transaction_number' => TransactionHelper::generateUniqueTrxId(),
            'name' => $this->name,
            'phone' => $this->no_hp,
            'address' => $this->alamat,
            'total' => $this->total_price,
            'cash_received' => $this->is_cash ? $this->getCashReceivedNumeric() : $this->total_price,
            'change' => $this->change,
        ]);

        $superAdmins = User::role('super_admin')->get();
        if ($superAdmins->isNotEmpty()) {
            LaravelNotification::send($superAdmins, new TransaksiBaruDibuat($order));
        }

        foreach ($this->order_items as $item) {
            $profit = ($item['selling_price'] - $item['cost_price']) * $item['quantity'];
            TransactionItem::create([
                'transaction_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['selling_price'],
                'cost_price' => $item['cost_price'],
                'total_profit' => $profit,
                'weight_gram' => $item['berat'],
            ]);
        }

        $this->orderToPrint = $order->id;
        $this->showConfirmationModal = true;
        $this->showCheckoutModal = false;
        Notification::make()->title('Order berhasil disimpan')->success()->send();
        $this->resetOrder();
    }

    public function printLocalKabel()
    {
        $directPrint = app(DirectPrintService::class);
        $directPrint->print($this->orderToPrint);
        $this->showConfirmationModal = false;
        $this->orderToPrint = null;
    }

    public function printBluetooth()
    {
        $order = Transaction::with(['paymentMethod', 'transactionItems.product'])->findOrFail($this->orderToPrint);
        $this->dispatch(
            'doPrintReceipt',
            store: Setting::first(),
            order: $order,
            items: $order->transactionItems,
            date: $order->created_at->format('d-m-Y H:i:s')
        );
        $this->showConfirmationModal = false;
        $this->orderToPrint = null;
    }
}