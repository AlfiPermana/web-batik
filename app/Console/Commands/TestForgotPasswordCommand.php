<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class TestForgotPasswordCommand extends Command
{
    protected $signature = 'forgot-password:test {email?}';
    protected $description = 'Test forgot password flow - sends reset email to user';

    public function handle()
    {
        // Get test email
        $testEmail = $this->argument('email') ?? 'test@example.com';
        
        $this->info('🔍 Testing Forgot Password Flow...');
        $this->line('');
        
        // Check if user exists
        $user = User::where('email', $testEmail)->first();
        
        if (!$user) {
            $this->error('❌ User dengan email ' . $testEmail . ' tidak ditemukan!');
            $this->info('');
            $this->info('📋 Daftar user yang ada:');
            
            User::select('id', 'name', 'email')->get()->each(function($u) {
                $this->line('   • ' . $u->email . ' (' . $u->name . ')');
            });
            
            return 1;
        }
        
        $this->info('✅ User ditemukan: ' . $user->name . ' (' . $user->email . ')');
        $this->line('');
        
        try {
            $this->info('📧 Mengirim password reset email...');
            $this->line('');
            
            // Send password reset link
            $response = Password::sendResetLink(['email' => $testEmail]);
            
            if ($response == Password::RESET_LINK_SENT) {
                $this->info('✅ Password reset link berhasil dikirim!');
                $this->line('');
                $this->info('📬 Periksa email: ' . $testEmail);
                $this->line('');
                $this->info('📋 Langkah selanjutnya:');
                $this->line('   1. Cek inbox email untuk reset link');
                $this->line('   2. Click link yang dikirim');
                $this->line('   3. Masukkan password baru');
                $this->line('   4. Login dengan password baru');
                $this->line('');
                $this->info('💡 Token reset password berlaku selama 60 menit');
                
                return 0;
            } else {
                $this->error('❌ Gagal mengirim reset link: ' . $response);
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->line('');
            $this->error('Troubleshooting:');
            $this->line('1. Pastikan email configuration sudah benar (jalankan: php artisan email:test)');
            $this->line('2. Pastikan user email sudah terdaftar');
            $this->line('3. Pastikan internet connection aktif');
            
            return 1;
        }
    }
}
