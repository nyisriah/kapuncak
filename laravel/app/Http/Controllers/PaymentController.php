<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Menampilkan form upload bukti pembayaran
     */
    public function create($booking_id)
    {
        // 🔍 DIAGNOSTIC LOGGING - Debug 403 di production
        \Illuminate\Support\Facades\Log::channel('daily')->info('Payment create accessed', [
            'booking_id' => $booking_id,
            'auth_check' => auth()->check(),
            'auth_user_id' => auth()->id(),
            'auth_user_email' => auth()->user()?->email,
            'auth_user_role' => auth()->user()?->role,
            'email_verified' => auth()->user()?->hasVerifiedEmail(),
            'session_id' => session()->getId(),
        ]);

        $user = auth()->user();
        $booking = Booking::with('villa')->findOrFail($booking_id);

        // Validasi kepemilikan
        if ($booking->user_id !== $user->id) {
            \Illuminate\Support\Facades\Log::channel('daily')->warning('Payment create: ownership check failed', [
                'booking_user_id' => $booking->user_id,
                'auth_user_id' => $user->id,
            ]);
            abort(403, 'Unauthorized');
        }

        // ✅ ENHANCED VALIDATION: Check if booking is expired
        if ($booking->isExpired()) {
            return redirect('/dashboard')->withErrors([
                'error' => 'Booking has expired. Payment deadline was ' . 
                         $booking->payment_deadline->format('d M Y H:i')
            ]);
        }

        // Validasi status: User hanya bisa upload jika admin sudah approve booking
        if (!$booking->canAccessPayment()) {
            \Illuminate\Support\Facades\Log::channel('daily')->warning('Payment create: status check failed', [
                'booking_status' => $booking->status,
                'can_access_payment' => $booking->canAccessPayment(),
            ]);
            return redirect('/dashboard')->withErrors(['error' => 'Booking must be approved and not expired to upload payment.']);
        }

        return view('payment.create', compact('booking'));
    }

    /**
     * Memproses upload bukti pembayaran
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        // 1. Validasi Input
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $booking = Booking::findOrFail($request->booking_id);

        // 2. Keamanan: Cek kepemilikan booking
        if ($booking->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        // ✅ ENHANCED VALIDATION: Check if booking is expired
        if ($booking->isExpired()) {
            return back()->withErrors([
                'error' => 'Booking has expired. Payment deadline was ' . 
                         $booking->payment_deadline->format('d M Y H:i')
            ]);
        }

        // 3. Keamanan: Cek status booking using enhanced method
        if (!$booking->canAccessPayment()) {
            return back()->withErrors(['error' => 'Booking must be approved and not expired for payment.']);
        }

        // 4. Proses File
        if ($request->hasFile('proof')) {
            // Simpan file ke storage/public/payments
            $proofPath = $request->file('proof')->store('payments', 'public');

            // 5. Update atau Buat Record Payment
            // Gunakan updateOrCreate agar tidak terjadi duplikasi data payment untuk 1 booking
            $payment = Payment::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'amount' => $booking->total_price,
                    'proof' => $proofPath,
                    'status' => 'pending',
                    'payment_method' => 'manual_transfer',
                    'rejection_reason' => null, // Clear any previous rejection reason
                ]
            );

            // 6. Update Status Booking menjadi 'paid'
            $booking->update(['status' => 'paid']);

            // 7. Redirect ke Dashboard
            return redirect()->route('dashboard')->with('success', 'Bukti pembayaran berhasil diupload! Admin akan segera memverifikasi.');
        }

        return back()->withErrors(['proof' => 'Gagal mengupload file bukti pembayaran.']);
    }

    /**
     * Menampilkan detail pembayaran
     */
    public function show($id)
    {
        $user = auth()->user();
        $payment = Payment::with('booking.villa')->findOrFail($id);

        if ($payment->booking->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        return view('payment.show', compact('payment'));
    }
}
