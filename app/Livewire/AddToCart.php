<?php

namespace App\Livewire;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class AddToCart extends Component
{
    public $productId = null;
    public $sizeId = null;
    public $quantity = 1;
    public $showModal = false;
    public $compact = false;
    public $productData = null;
    public $sizes = [];


    public function mount($productId = null, $compact = false)
    {
        $this->productId = $productId;
        $this->compact = $compact;

        if (!$this->productId) {
            return;
        }

        $product = Product::with('sizes')->find($this->productId);
        if (!$product) {
            return;
        }

        $this->productData = [
            'id' => $product->id,
            'title' => $product->title,
            'description' => $product->description,
        ];

        if ($product->sizes->count() > 0) {
            $this->sizeId = $product->sizes->first()->id;
        }

        $this->sizes = $product->sizes->map(fn($size) => [
            'id' => $size->id,
            'size' => $size->size,
            'price' => $size->price,
            'stock' => (int) ($size->stock ?? 0),
        ])->toArray();
    }

    #[On('product-size-selected')]
    public function onSizeSelected($payload = null): void
    {
        $sizeId = null;

        if (is_array($payload)) {
            $payloadProductId = $payload['productId'] ?? null;
            if ($payloadProductId !== null && (int) $payloadProductId !== (int) $this->productId) {
                return;
            }
            $sizeId = $payload['sizeId'] ?? null;
        } else {
            $sizeId = $payload;
        }

        if (is_numeric($sizeId)) {
            $this->sizeId = (int) $sizeId;
        }
    }

    #[On('quantity-updated')]
    public function onQuantityUpdated($payload = null)
    {
        if (is_array($payload)) {
            $productId = $payload['productId'] ?? null;
            if ($productId !== null && (int) $productId !== (int) $this->productId) {
                return;
            }
            $this->quantity = (int) ($payload['qty'] ?? 1);
        }
    }

    public function addToCart()
    {
        if (!Auth::check()) {
            return $this->redirect(route('login'), navigate: true);
        }

        try {
            $user = Auth::user();
            $cart = $user->cart ?: Cart::create(['user_id' => $user->id]);

            $product = Product::with('sizes')->find($this->productId);
            if (!$product) {
                $this->dispatch('alert', message: __('Product not found'), type: 'error');
                return;
            }

            $size = null;
            if ($this->sizeId) {
                $size = $product->sizes->firstWhere('id', (int) $this->sizeId);
            }
            $size = $size ?: $product->sizes->first();

            if (!$size) {
                $this->dispatch('alert', message: __('Product size not available'), type: 'error');
                return;
            }

            $stock = (int) ($size->stock ?? 0);
            if ($stock <= 0) {
                $this->dispatch('alert', type: 'error', message: 'Stok untuk ukuran ini sudah habis.');
                return;
            }

            $existing = $cart->items()
                ->where('product_id', $product->id)
                ->where('product_size_id', $size->id)
                ->first();

            $existingQty = $existing ? (int) $existing->quantity : 0;
            if ($existingQty >= $stock) {
                $this->dispatch('alert', type: 'error', message: 'Jumlah di keranjang sudah mencapai batas maksimal stok tersedia.');
                return;
            }

            $requestQty = (int) $this->quantity;
            if ($requestQty < 1) {
                $requestQty = 1;
            }

            if ($existingQty + $requestQty > $stock) {
                $toAdd = $stock - $existingQty;
                $cartItem = $cart->addItem($product, $size, $toAdd);
                if ($cartItem) {
                    $cartCount = $cart->items()->count();
                    $this->dispatch('alert', type: 'warning', message: 'Hanya ' . $toAdd . ' produk yang ditambahkan karena keterbatasan stok.');
                    $this->dispatch('cart-item-count', count: $cartCount);
                }
            } else {
                $cartItem = $cart->addItem($product, $size, $requestQty);
                if ($cartItem) {
                    $cartCount = $cart->items()->count();
                    $this->dispatch('alert', type: 'success', message: 'Produk berhasil ditambahkan ke keranjang!');
                    $this->dispatch('cart-item-count', count: $cartCount);
                } else {
                    $this->dispatch('alert', type: 'error', message: 'Gagal menambahkan produk ke keranjang');
                }
            }
        } catch (\Exception $e) {
            $this->dispatch('alert', message: __('Error: ') . $e->getMessage(), type: 'error');
        }
    }

    public function resetForm()
    {
        $this->quantity = 1;
        if ($this->productId) {
            $product = Product::with('sizes')->find($this->productId);
            $this->sizeId = $product?->sizes->first()?->id;
        }
    }

    public function render()
    {
        return view('livewire.add-to-cart', [
            'productData' => $this->productData,
            'sizes' => $this->sizes,
            'productId' => $this->productId,
            'sizeId' => $this->sizeId,
            'quantity' => $this->quantity,
            'showModal' => $this->showModal,
            'compact' => $this->compact,
        ]);
    }
}
