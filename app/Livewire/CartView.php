<?php

namespace App\Livewire;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CartView extends Component
{
    public $cart;
    public $cartItems = [];
    public $subtotal = 0;
    public $total = 0;

    protected $listeners = ['cart-updated' => 'loadCart'];

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        $user = Auth::user();
        
        if (!$user) {
            return;
        }

        // Get or create cart
        $this->cart = $user->cart ?: Cart::create(['user_id' => $user->id]);
        
        // Load cart items with relationships
        $this->cartItems = $this->cart->items()
            ->with(['product', 'size'])
            ->get()
            ->toArray();

        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->subtotal = (float) $this->cart->subtotal ?? 0;
        $this->total = (float) $this->cart->total ?? 0;
    }

    public function updateQuantity($itemId, $quantity)
    {
        if ($quantity < 1) {
            return;
        }

        $item = $this->cart->items()->find($itemId);
        
        if ($item) {
            $item->update(['quantity' => $quantity]);
            $this->dispatch('cart-updated');
            $this->loadCart();
            $this->dispatch('notify', message: __('Quantity updated'), type: 'success');
        }
    }

    public function removeItem($itemId)
    {
        $item = $this->cart->items()->find($itemId);
        
        if ($item) {
            $productName = $item->product->title;
            $item->delete();
            $this->dispatch('cart-updated');
            $this->loadCart();
            $this->dispatch('notify', message: __(':name removed from cart', ['name' => $productName]), type: 'success');
        }
    }

    public function clearCart()
    {
        $this->cart->clear();
        $this->dispatch('cart-updated');
        $this->loadCart();
        $this->dispatch('notify', message: __('Cart cleared'), type: 'success');
    }

    public function render()
    {
        return view('livewire.cart-view', [
            'cart' => $this->cart,
            'cartItems' => $this->cartItems,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
        ]);
    }
}
