<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class OrderHistory extends Component
{
    use WithPagination;

    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $filterStatus = 'all';

    protected $queryString = [
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'filterStatus' => ['except' => 'all'],
    ];

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function updatedSortDirection()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        /** @var User $user */
        $user = Auth::user();
        
        $query = $user->orders()
            ->with(['items.product.images', 'items.productSize'])
            ->orderBy($this->sortBy, $this->sortDirection);

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        $orders = $query->paginate(10);

        // Auto-mark expired payments (tanpa cron) berdasarkan expired_time/expired_at dari Tripay response
        foreach ($orders as $order) {
            if (!in_array($order->payment_status, ['unpaid', 'pending'], true)) {
                continue;
            }

            $tripay = $order->tripay_response;
            if (is_string($tripay)) {
                $tripay = json_decode($tripay, true);
            }

            if (!is_array($tripay)) {
                continue;
            }

            $expiresAt = null;
            try {
                if (!empty($tripay['expired_time']) && is_numeric($tripay['expired_time'])) {
                    $expiresAt = Carbon::createFromTimestamp((int) $tripay['expired_time']);
                } elseif (!empty($tripay['expired_at'])) {
                    $expiresAt = Carbon::parse($tripay['expired_at']);
                }
            } catch (\Throwable $e) {
                $expiresAt = null;
            }

            if ($expiresAt && $expiresAt->isPast()) {
                $order->forceFill(['payment_status' => 'expired'])->save();
                $order->payment_status = 'expired';
            }
        }

        return view('livewire.order-history', [
            'orders' => $orders,
        ]);
    }
}
