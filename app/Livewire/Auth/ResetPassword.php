<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Component;
use App\Models\User;

class ResetPassword extends Component
{
    public $token = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $error = null;
    public $invalidToken = false;

    protected $rules = [
        'email' => ['required', 'email'],
        'password' => ['required', 'min:8', 'confirmed'],
    ];

    public function mount($token = null)
    {
        // Check if token is provided
        if (!$token) {
            $this->invalidToken = true;
            return;
        }

        $this->token = $token;
        
        // Get email from query parameter if available
        $this->email = request()->query('email', '');
    }

    public function resetPassword()
    {
        $this->validate();

        // Attempt to reset the password
        $status = Password::reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            // Redirect to login with success message
            return redirect()->route('login')->with('status', 'Password Anda berhasil direset. Silakan login dengan password baru Anda.');
        } else {
            $this->error = 'Token reset password tidak valid atau telah kadaluarsa. Silakan minta ulang link reset password.';
        }
    }

    public function render()
    {
        return view('livewire.auth.reset-password');
    }
}
