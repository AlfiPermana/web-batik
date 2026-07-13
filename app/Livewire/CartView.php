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

    /**
     * Remove the redundant listener that causes double loading.
     * The component manually calls loadCart() after updates.
     */
    // protected $listeners = ['cart-updated' => 'loadCart'];

    public function mount()
    {
        $this->loadCart();
    }

    /**
     * Fetch fresh cart data from the database.
     */
    public function loadCart()
    {
        $user = Auth::user();
        
        if (!$user) {
            return;
        }

        // Get or create cart for the logged-in user
        $this->cart = $user->cart ?: Cart::create(['user_id' => $user->id]);
        
        /**
         * Refresh the cart model to ensure relationship data is not stale.
         * This is crucial after quantity updates to ensure totals and items match DB.
         */
        $this->cart->refresh();
        $this->cart->load(['items.product', 'items.size']);

        // Set the public array property for the view
        $this->cartItems = $this->cart->items->toArray();

        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        // Re-assign totals from the refreshed cart model
        $this->subtotal = (float) $this->cart->subtotal;
        $this->total = (float) $this->cart->total;
    }

    /**
     * Primary method to update item quantity with stock validation.
     */
    public function updateQuantity($itemId, $quantity)
    {
        $quantity = (int) $quantity;
        if ($quantity < 1) {
            return;
        }

        // Use relation query to ensure we find the item belonging to this cart
        $item = $this->cart->items()->with(['size'])->find($itemId);
        
        if ($item) {
            // Validate against available stock
            $stock = $item->size ? (int) ($item->size->stock ?? 0) : 0;
            if ($quantity > $stock) {
                $quantity = $stock;
                $this->dispatch('notify', message: 'Jumlah produk disesuaikan dengan stok tersedia (' . $stock . ')', type: 'warning');
            }
            
            // Update the quantity in database
            $item->update(['quantity' => $quantity]);
            
            // Notify other components (e.g. Navigation Cart Count)
            $this->dispatch('cart-updated');
            
            // Reload local state
            $this->loadCart();
            $this->dispatch('notify', message: 'Keranjang berhasil diperbarui', type: 'success');
        }
    }

    /**
     * Increment quantity safely without relying on stale client-side values.
     */
    public function incrementQuantity($itemId)
    {
        $item = $this->cart->items()->find($itemId);
        if ($item) {
            $this->updateQuantity($itemId, $item->quantity + 1);
        }
    }

    /**
     * Decrement quantity safely with a minimum of 1.
     */
    public function decrementQuantity($itemId)
    {
        $item = $this->cart->items()->find($itemId);
        if ($item) {
            if ($item->quantity > 1) {
                $this->updateQuantity($itemId, $item->quantity - 1);
            }
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
