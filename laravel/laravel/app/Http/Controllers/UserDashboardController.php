<?php

namespace App\Http\Controllers;

use App\Models\Booking;

class UserDashboardController extends Controller
{
    public function index()
    {
        $bookings = Booking::where('user_id', auth()->id())
            ->with(['villa', 'payment'])
            ->latest()
            ->get();

        return view('dashboard.index', compact('bookings'));
    }
}