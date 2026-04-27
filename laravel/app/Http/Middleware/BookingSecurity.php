<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Booking;

class BookingSecurity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $bookingId = $request->route('booking') ?? $request->route('id');
        
        if ($bookingId) {
            $booking = Booking::find($bookingId);
            
            if (!$booking) {
                return response()->json(['error' => 'Booking not found'], 404);
            }

            // ✅ SECURITY: Check if booking is expired before payment operations
            if ($request->isMethod('POST') && $request->routeIs('payments.*')) {
                if ($booking->isExpired()) {
                    return back()->withErrors([
                        'error' => 'Booking has expired. Payment deadline was ' . 
                                 $booking->payment_deadline->format('d M Y H:i')
                    ]);
                }
            }

            // ✅ SECURITY: Prevent double payment uploads
            if ($request->routeIs('payments.store') && $booking->payment && $booking->payment->status === 'pending') {
                return back()->withErrors([
                    'error' => 'Payment already uploaded and pending verification.'
                ]);
            }
        }

        return $next($request);
    }
}
