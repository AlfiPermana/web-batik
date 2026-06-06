<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class TestEmailCommand extends Command
{
    protected $signature = 'email:test {email?}';
    protected $description = 'Test email configuration by sending a test email';

    public function handle()
    {
        $testEmail = $this->argument('email') ?? config('mail.from.address');
        
        $this->info('🔍 Testing Email Configuration...');
        $this->line('');
        
        $this->line('Mail Driver: ' . config('mail.default'));
        $this->line('From Address: ' . config('mail.from.address'));
        $this->line('From Name: ' . config('mail.from.name'));
        $this->line('SMTP Host: ' . config('mail.mailers.smtp.host'));
        $this->line('SMTP Port: ' . config('mail.mailers.smtp.port'));
        $this->line('SMTP Username: ' . (config('mail.mailers.smtp.username') ? '✓ Configured' : '✗ Not configured'));
        $this->line('');
        
        // Validate SMTP credentials
        if (empty(config('mail.mailers.smtp.username'))) {
            $this->error('❌ MAIL_USERNAME tidak dikonfigurasi!');
            $this->info('Update .env Anda dengan: MAIL_USERNAME=your-email@gmail.com');
            return 1;
        }
        
        if (strpos(config('mail.mailers.smtp.username'), '@') === false) {
            $this->error('❌ MAIL_USERNAME bukan format email yang valid!');
            return 1;
        }
        
        if (empty(config('mail.mailers.smtp.password'))) {
            $this->error('❌ MAIL_PASSWORD tidak dikonfigurasi!');
            $this->info('Update .env Anda dengan: MAIL_PASSWORD=your-google-app-password');
            return 1;
        }
        
        $this->info('✅ Konfigurasi email terlihat valid');
        $this->line('');
        
        try {
            $this->info('📧 Mengirim test email ke: ' . $testEmail);
            
            Mail::raw('Ini adalah test email dari Batik Giri Alam Password Reset System. Jika Anda melihat pesan ini, email configuration Anda berhasil! ✅', function (Message $message) use ($testEmail) {
                $message->to($testEmail)
                        ->subject('✅ Test Email - Batik Giri Alam Password Reset');
            });
            
            $this->info('✅ Email berhasil dikirim!');
            $this->line('');
            $this->info('📬 Periksa inbox Anda di: ' . $testEmail);
            $this->line('');
            $this->info('💡 Tips:');
            $this->line('   - Tunggu maksimal 5 menit untuk email masuk');
            $this->line('   - Cek folder Spam/Junk jika tidak ada di Inbox');
            $this->line('   - Pastikan MAIL_FROM_ADDRESS sesuai dengan email pengirim');
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Error saat mengirim email:');
            $this->error($e->getMessage());
            $this->line('');
            $this->error('📋 Troubleshooting:');
            $this->line('1. Apakah MAIL_USERNAME sudah diisi dengan email Gmail Anda?');
            $this->line('2. Apakah MAIL_PASSWORD sudah diisi dengan App Password Google?');
            $this->line('3. Apakah Anda sudah jalankan: php artisan config:clear');
            $this->line('4. Apakah Gmail account punya 2FA enabled? (diperlukan untuk App Password)');
            $this->line('5. Pastikan internet connection aktif');
            
            return 1;
        }
    }
}
