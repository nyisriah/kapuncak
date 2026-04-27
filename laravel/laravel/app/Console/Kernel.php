<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // ✅ Auto-cancel expired bookings every 5 minutes
        $schedule->call(function () {
            $this->cancelExpiredBookings();
        })->everyFiveMinutes()
          ->name('cancel-expired-bookings')
          ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    /**
     * Cancel bookings that have passed their payment deadline
     */
    protected function cancelExpiredBookings(): void
    {
        $expiredBookings = \App\Models\Booking::where('status', 'approved')
            ->whereNotNull('payment_deadline')
            ->where('payment_deadline', '<', now())
            ->get();

        foreach ($expiredBookings as $booking) {
            try {
                // Only cancel if not already paid or confirmed
                if (!in_array($booking->status, ['paid', 'confirmed', 'cancelled'])) {
                    $booking->cancel();
                    
                    // Log the cancellation
                    \Illuminate\Support\Facades\Log::info('Booking automatically cancelled due to expired payment deadline', [
                        'booking_id' => $booking->id,
                        'user_id' => $booking->user_id,
                        'villa_id' => $booking->villa_id,
                        'payment_deadline' => $booking->payment_deadline,
                        'cancelled_at' => now(),
                    ]);

                    // Optional: Send notification to user
                    if ($booking->user) {
                        // You can add email notification here if needed
                        // $booking->user->notify(new BookingExpiredNotification($booking));
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to cancel expired booking', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($expiredBookings->count() > 0) {
            \Illuminate\Support\Facades\Log::info('Expired bookings processed', [
                'total_processed' => $expiredBookings->count(),
                'timestamp' => now(),
            ]);
        }
    }
}
