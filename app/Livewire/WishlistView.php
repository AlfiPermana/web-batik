<?php

namespace App\Livewire;

use App\Models\WishlistItem;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class WishlistView extends Component
{
    use WithPagination;

    protected $listeners = ['wishlist-updated' => '$refresh'];

    public function render()
    {
        $userId = Auth::id();

        $items = WishlistItem::query()
            ->where('user_id', $userId)
            ->with(['product.images', 'product.sizes'])
            ->latest()
            ->paginate(12);

        return view('livewire.wishlist-view', [
            'items' => $items,
        ]);
    }
}
