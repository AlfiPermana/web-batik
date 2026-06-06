<?php

namespace App\Livewire;

use App\Models\WishlistItem;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ToggleWishlist extends Component
{
    public $productId;
    public $compact = false;
    public $isWishlisted = false;

    protected $listeners = ['wishlist-refresh' => '$refresh'];

    public function mount($productId, $compact = false)
    {
        $this->productId = $productId;
        $this->compact = $compact;

        $this->isWishlisted = Auth::check()
            ? WishlistItem::where('user_id', Auth::id())
                ->where('product_id', $this->productId)
                ->exists()
            : false;
    }

    public function toggle()
    {
        if (!Auth::check()) {
            return $this->redirect(route('login'), navigate: true);
        }

        $userId = Auth::id();

        $existing = WishlistItem::where('user_id', $userId)
            ->where('product_id', $this->productId)
            ->first();

        if ($existing) {
            $existing->delete();
            $this->isWishlisted = false;
            $this->dispatch('alert', type: 'success', message: 'Wishlist dihapus');
        } else {
            WishlistItem::create([
                'user_id' => $userId,
                'product_id' => $this->productId,
            ]);
            $this->isWishlisted = true;
            $this->dispatch('alert', type: 'success', message: 'Ditambahkan ke wishlist');
        }

        // refresh any wishlist UI
        $this->dispatch('wishlist-updated');
        $this->dispatch('wishlist-refresh');
    }

    public function render()
    {
        return view('livewire.toggle-wishlist');
    }
}
