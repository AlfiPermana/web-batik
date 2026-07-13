<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Register extends Component
{
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $phone_number = '';

    protected $rules = [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
        'phone_number' => ['required', 'string', 'max:25'],
    ];

    public function register()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => 'customer',
        ]);

        // create associated customer profile
        if (method_exists($user, 'customerProfile')) {
            $user->customerProfile()->create([
                'phone_number' => $this->phone_number,
            ]);
        }

        Auth::login($user);

        return redirect()->route('verification.notice');
    }

    public function render()
    {
        // Livewire component view lives under resources/views/livewire/auth/register.blade.php
        return view('livewire.auth.register');
    }
}