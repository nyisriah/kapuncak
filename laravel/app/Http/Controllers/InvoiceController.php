<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display the invoice for a confirmed booking
     */
    public function show($id)
    {
        $booking = Booking::with(['user', 'villa', 'payment'])->findOrFail($id);

        // Check if user owns the booking or is admin
        if ($booking->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access to invoice');
        }

        // Check if status is confirmed
        if ($booking->status !== 'confirmed') {
            abort(404, 'Invoice not available - booking not confirmed');
        }

        return view('invoice.show', compact('booking'));
    }

    /**
     * Download invoice as PDF (optional enhancement)
     */
    public function download($id)
    {
        $booking = Booking::with(['user', 'villa', 'payment'])->findOrFail($id);

        // Check if user owns the booking or is admin
        if ($booking->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized access to invoice');
        }

        // Check if status is confirmed
        if ($booking->status !== 'confirmed') {
            abort(404, 'Invoice not available - booking not confirmed');
        }

        // For now, redirect to show view (PDF generation can be added later)
        return redirect()->route('invoice.show', $id);
    }

    /**
     * Generate invoice number
     */
    public static function generateInvoiceNumber(Booking $booking): string
    {
        return 'INV-' . $booking->id . '-' . $booking->created_at->format('Ymd');
    }
}