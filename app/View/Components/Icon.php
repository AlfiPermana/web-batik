<?php

namespace App\View\Components;

use Illuminate\Support\Facades\File;
use Illuminate\View\Component;

class Icon extends Component
{
    public string $url;

    public function __construct(
        public string $method,
    ) {
        $this->url = $this->getIconUrl($method);
    }

    public function getIconUrl(string $id): string
    {
        $lower = strtolower($id);

        $iconMap = [
            'bcava'      => 'assets/bank/bca.png',
            'briva'      => 'assets/bank/bri.png',
            'bniva'      => 'assets/bank/bni.png',
            'mandiriva'  => 'assets/bank/mandiri.png',
            'qris'       => 'assets/qris/qris.png',
            'ovo'        => 'assets/ewallet/ovo.png',
            'dana'       => 'assets/ewallet/dana.png',
            'linkaja'    => 'assets/ewallet/linkaja.png',

            // Tripay biasanya pakai kode ALFACART (bukan ALFAMART)
            'alfacart'   => 'assets/retail/alfamart.png',
            'alfamart'   => 'assets/retail/alfamart.png',
            'indomaret'  => 'assets/retail/indomaret.png',
        ];


        $path = $iconMap[$lower] ?? 'assets/icons/placeholder.svg';

        if (!File::exists(public_path($path))) {
            $path = 'assets/icons/placeholder.svg';
        }

        return asset($path);



    }

    // ✅ Helper static method — bisa dipakai di Livewire!
    public static function getUrl(string $methodId): string
    {
        $instance = new self($methodId);
        return $instance->url;
    }

    public function render()
    {
        return view('components.icon');
    }
}