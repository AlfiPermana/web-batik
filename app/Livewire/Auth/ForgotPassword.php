<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Component;

class ForgotPassword extends Component
{
    public $email = '';
    public $submitted = false;
    public $status = null;
    public $error = null;

    protected $rules = [
        'email' => ['required', 'email'],
    ];

    public function sendResetLink()
    {
        $this->validate();

        // Send password reset link
        $status = Password::sendResetLink([
            'email' => $this->email,
        ]);

        if ($status === Password::RESET_LINK_SENT) {
            $this->submitted = true;
            $this->status = 'Link untuk reset password telah dikirim ke email Anda. Silakan cek inbox atau folder spam Anda.';
            $this->error = null;
        } else {
            $this->error = 'Gagal mengirim link reset password. Silakan cek email Anda dan coba lagi.';
            $this->status = null;
        }
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
