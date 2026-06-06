<?php

namespace App\Jobs;

use App\Models\WorkshopPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPaymentConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payment;

    /**
     * Create a new job instance.
     */
    public function __construct(WorkshopPayment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Processing payment confirmation', [
                'payment_id' => $this->payment->id,
                'reference' => $this->payment->reference_number
            ]);

            // Update booking status
            $booking = $this->payment->booking;
            
            if ($booking) {
                $booking->update([
                    'status' => 'confirmed',
                    'payment_status' => $this->payment->type === 'deposit' ? 'deposit_paid' : 'fully_paid',
                    'payment_date' => now(),
                ]);

                Log::info('Booking updated', [
                    'booking_id' => $booking->id,
                    'status' => 'confirmed'
                ]);

                // Send email notification atau reminder di sini
                // Contoh: Mail::send(new PaymentConfirmedMail($booking));
            }

            Log::info('Payment confirmation completed successfully', [
                'payment_id' => $this->payment->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error processing payment confirmation', [
                'payment_id' => $this->payment->id,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Jangan throw - job sudah dilakukan yang terbaik
            // Tripay sudah dapat response 200 OK
        }
    }
}
