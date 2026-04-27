<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\BookingDate;
use App\Models\Villa;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();

        // ✅ VALIDASI
        $request->validate([
            'villa_id' => 'required|exists:villas,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guest' => 'required|integer|min:1',
        ]);

        $villa = Villa::findOrFail($request->villa_id);

        // ✅ VALIDASI KAPASITAS
        if ($request->guest > $villa->max_guests) {
            return back()->withErrors([
                'guest' => 'Jumlah tamu melebihi kapasitas villa'
            ]);
        }

        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);

        // 🔥 CEK TANGGAL BENTROK
        $isBooked = Booking::where('villa_id', $villa->id)
            ->where(function ($q) use ($checkIn, $checkOut) {
                $q->whereBetween('checkin_date', [$checkIn, $checkOut])
                  ->orWhereBetween('checkout_date', [$checkIn, $checkOut])
                  ->orWhere(function ($q2) use ($checkIn, $checkOut) {
                      $q2->where('checkin_date', '<=', $checkIn)
                         ->where('checkout_date', '>=', $checkOut);
                  });
            })
            ->exists();

        if ($isBooked) {
            return back()->withErrors([
                'date' => 'Tanggal sudah dibooking, pilih tanggal lain'
            ]);
        }

        // 🔥 AUTO PRICING
        $totalPrice = 0;
        for ($date = $checkIn->copy(); $date->lt($checkOut); $date->addDay()) {
            $totalPrice += $villa->getPriceForDate($date->toDateString());
        }

        DB::beginTransaction();

        try {
            // 🔒 DOUBLE CHECK (ANTI DOUBLE BOOKING)
            $lockCheck = Booking::where('villa_id', $villa->id)
                ->where(function ($q) use ($checkIn, $checkOut) {
                    $q->whereBetween('checkin_date', [$checkIn, $checkOut])
                      ->orWhereBetween('checkout_date', [$checkIn, $checkOut]);
                })
                ->lockForUpdate()
                ->exists();

            if ($lockCheck) {
                DB::rollBack();
                return back()->withErrors([
                    'date' => 'Barusan dibooking orang lain, coba tanggal lain'
                ]);
            }

            // ✅ SIMPAN
            $booking = Booking::create([
                'user_id' => $user->id,
                'villa_id' => $villa->id,
                'checkin_date' => $checkIn,
                'checkout_date' => $checkOut,
                'guest' => $request->guest,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'user_ip' => $request->ip(),
            ]);

            // 🔥 BUAT BOOKING DATES untuk setiap tanggal
            for ($date = $checkIn->copy(); $date->lt($checkOut); $date->addDay()) {
                BookingDate::create([
                    'booking_id' => $booking->id,
                    'villa_id' => $villa->id,
                    'date' => $date->toDateString(),
                ]);
            }

            DB::commit();

            // 🔥 FIX: redirect ke dashboard biar keliatan hasilnya
            return redirect('/dashboard')
                ->with('success', 'Booking berhasil!');

        } catch (\Exception $e) {
            DB::rollBack();

            // 🔥 FIX: tampilkan error asli (sementara buat debug)
            return back()->withErrors([
                'error' => $e->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        $booking = Booking::with('villa')->findOrFail($id);

        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }

        return view('booking.show', compact('booking'));
    }
}