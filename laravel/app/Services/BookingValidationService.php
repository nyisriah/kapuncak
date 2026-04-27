<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BookingValidationService
{
    /**
     * Validate booking can be approved
     */
    public static function canApproveBooking(Booking $booking): array
    {
        $errors = [];

        if (!$booking->canTransitionTo('approved')) {
            $errors[] = 'Booking cannot be approved from current status: ' . $booking->status;
        }

        if ($booking->isExpired()) {
            $errors[] = 'Booking has expired and cannot be approved';
        }

        return $errors;
    }

    /**
     * Validate payment can be processed
     */
    public static function canProcessPayment(Booking $booking): array
    {
        $errors = [];

        if ($booking->user_id !== auth()->id()) {
            $errors[] = 'Unauthorized access to booking';
        }

        if (!$booking->canAccessPayment()) {
            if ($booking->isExpired()) {
                $errors[] = 'Booking has expired. Payment deadline was ' . 
                           $booking->payment_deadline->format('d M Y H:i');
            } else {
                $errors[] = 'Booking must be approved to process payment';
            }
        }

        // Prevent duplicate payment uploads
        if ($booking->payment && $booking->payment->status === 'pending') {
            $errors[] = 'Payment already uploaded and pending verification';
        }

        return $errors;
    }

    /**
     * Validate payment approval
     */
    public static function canApprovePayment(Payment $payment): array
    {
        $errors = [];

        if ($payment->status !== 'pending') {
            $errors[] = 'Only pending payments can be approved';
        }

        if (!$payment->booking) {
            $errors[] = 'Payment has no associated booking';
        }

        if ($payment->booking->status !== 'paid') {
            $errors[] = 'Booking status must be paid to approve payment';
        }

        return $errors;
    }

    /**
     * Validate payment rejection
     */
    public static function canRejectPayment(Payment $payment, ?string $reason = null): array
    {
        $errors = [];

        if ($payment->status !== 'pending') {
            $errors[] = 'Only pending payments can be rejected';
        }

        if (empty($reason)) {
            $errors[] = 'Rejection reason is required';
        }

        if (strlen($reason) < 5) {
            $errors[] = 'Rejection reason must be at least 5 characters';
        }

        return $errors;
    }

    /**
     * Safe booking status transition with logging
     */
    public static function safeStatusTransition(Booking $booking, string $newStatus, string $context = ''): bool
    {
        try {
            $oldStatus = $booking->status;
            
            if (!$booking->canTransitionTo($newStatus)) {
                Log::warning('Invalid booking status transition attempted', [
                    'booking_id' => $booking->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'context' => $context,
                    'user_id' => auth()->id(),
                ]);
                return false;
            }

            $success = $booking->update(['status' => $newStatus]);
            
            if ($success) {
                Log::info('Booking status transition successful', [
                    'booking_id' => $booking->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'context' => $context,
                    'user_id' => auth()->id(),
                ]);
            }

            return $success;
        } catch (\Exception $e) {
            Log::error('Error during booking status transition', [
                'booking_id' => $booking->id,
                'old_status' => $booking->status,
                'new_status' => $newStatus,
                'context' => $context,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Check for potential data inconsistencies
     */
    public static function validateBookingDataIntegrity(Booking $booking): array
    {
        $warnings = [];

        // Check for missing required relationships
        if (!$booking->user) {
            $warnings[] = 'Booking has no associated user';
        }

        if (!$booking->villa) {
            $warnings[] = 'Booking has no associated villa';
        }

        // Check for logical inconsistencies
        if ($booking->checkin_date && $booking->checkout_date) {
            if ($booking->checkin_date->gte($booking->checkout_date)) {
                $warnings[] = 'Check-in date is after or equal to check-out date';
            }
        }

        // Check payment status consistency
        if ($booking->payment && $booking->status === 'confirmed' && $booking->payment->status !== 'success') {
            $warnings[] = 'Confirmed booking has non-successful payment';
        }

        return $warnings;
    }
}
