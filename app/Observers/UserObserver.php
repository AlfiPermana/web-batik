<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Cart;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Auto-create cart for new user
        if ($user->isCustomer()) {
            Cart::create(['user_id' => $user->id]);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // If user becomes customer and doesn't have cart, create one
        if ($user->isCustomer() && !$user->cart) {
            Cart::create(['user_id' => $user->id]);
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // Cart will be deleted automatically via cascade
    }
}
