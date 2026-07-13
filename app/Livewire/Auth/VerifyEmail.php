<?php

namespace App\Livewire\Auth;

use App\Mail\VerificationCodeMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class VerifyEmail extends Component
{
    public $code = '';

    public function mount()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->email_verified_at) {
            return redirect()->route('customer.dashboard');
        }
    }

    public function verify()
    {
        $this->validate([
            'code' => ['required', 'string', 'size:6'],
        ], [
            'code.required' => 'Kode verifikasi wajib diisi.',
            'code.size' => 'Kode verifikasi harus 6 digit.',
        ]);

        $user = Auth::user();

        if (!$user->verification_code || $user->verification_code !== $this->code) {
            $this->addError('code', 'Kode verifikasi tidak cocok.');
            return;
        }

        if ($user->verification_code_expires_at && now()->gt($user->verification_code_expires_at)) {
            $this->addError('code', 'Kode verifikasi telah kadaluarsa. Silakan kirim ulang kode.');
            return;
        }

        // Mark as verified - Bypassing mass-assignment protection
        $user->email_verified_at = now();
        $user->verification_code = null;
        $user->verification_code_expires_at = null;
        $user->save();

        session()->flash('success', 'Email Anda berhasil diverifikasi!');

        return redirect()->route('customer.dashboard');
    }

    public function resendCode($notifyUser = true)
    {
        $user = Auth::user();
        
        $newCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $user->update([
            'verification_code' => $newCode,
            'verification_code_expires_at' => now()->addMinutes(15),
        ]);

        try {
            Mail::to($user->email)->send(new VerificationCodeMail($newCode, $user->name));
            if ($notifyUser) {
                session()->flash('success', 'Kode verifikasi baru telah dikirim ke email Anda.');
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal mengirim email verifikasi: ' . $e->getMessage());
            if ($notifyUser) {
                session()->flash('error', 'Gagal mengirim email verifikasi. Silakan coba lagi nanti.');
            }
        }
        
        $this->code = '';
    }

    public function render()
    {
        return view('livewire.auth.verify-email');
    }
}
