<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Swift_SmtpTransport;

class DiagnoseEmailCommand extends Command
{
    protected $signature = 'email:diagnose';
    protected $description = 'Diagnose email configuration and SMTP connection';

    public function handle()
    {
        $this->info('🔍 Email Configuration Diagnostic');
        $this->line('═══════════════════════════════════════════════════════════');
        $this->line('');
        
        // Check config
        $this->info('📋 Configuration:');
        $this->line('Mail Driver: ' . config('mail.default'));
        $this->line('SMTP Host: ' . config('mail.mailers.smtp.host'));
        $this->line('SMTP Port: ' . config('mail.mailers.smtp.port'));
        $this->line('SMTP Encryption: ' . (config('mail.mailers.smtp.encryption') ?? 'tls (default)'));
        $this->line('Username: ' . config('mail.mailers.smtp.username'));
        $this->line('From Address: ' . config('mail.from.address'));
        $this->line('');
        
        // Test SMTP Connection
        $this->info('🔌 Testing SMTP Connection...');
        try {
            $transport = new Swift_SmtpTransport(
                config('mail.mailers.smtp.host'),
                config('mail.mailers.smtp.port'),
                config('mail.mailers.smtp.encryption') ?? 'tls'
            );
            
            $transport->setUsername(config('mail.mailers.smtp.username'));
            $transport->setPassword(config('mail.mailers.smtp.password'));
            
            // Try to connect
            $transport->start();
            
            $this->info('✅ SMTP Connection successful!');
            $transport->stop();
            
        } catch (\Exception $e) {
            $this->error('❌ SMTP Connection failed!');
            $this->error('Error: ' . $e->getMessage());
            $this->line('');
            $this->error('Possible solutions:');
            $this->line('1. Check internet connection');
            $this->line('2. Check MAIL_USERNAME and MAIL_PASSWORD in .env');
            $this->line('3. Ensure Gmail 2FA is enabled');
            $this->line('4. Ensure App Password is used (not regular password)');
            $this->line('5. Check firewall/antivirus blocking port 587');
            return 1;
        }
        
        // Test sending email
        $this->info('');
        $this->info('📧 Testing Email Send...');
        try {
            Mail::raw('Test email from diagnostic command.', function ($message) {
                $message->to(config('mail.from.address'))
                    ->subject('Diagnostic Test Email');
            });
            
            $this->info('✅ Email sent successfully!');
            $this->line('');
            $this->info('📬 Check inbox: ' . config('mail.from.address'));
            $this->line('');
            $this->info('💡 Notes:');
            $this->line('• Email may take 1-5 minutes to arrive');
            $this->line('• Check spam/junk folder');
            $this->line('• Check email log file: storage/logs/');
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to send email!');
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
        
        $this->line('');
        $this->line('═══════════════════════════════════════════════════════════');
        
        return 0;
    }
}
