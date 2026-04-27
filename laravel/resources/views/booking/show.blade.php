@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-10">

    <div class="bg-white rounded-2xl shadow p-6">

        <h1 class="text-2xl font-bold mb-6">
            Detail Booking
        </h1>

        <div class="space-y-4">
            <div>
                <strong>Villa:</strong> {{ $booking->villa->name }}
            </div>
            <div>
                <strong>Check-in:</strong> {{ $booking->check_in->format('d M Y') }}
            </div>
            <div>
                <strong>Check-out:</strong> {{ $booking->check_out->format('d M Y') }}
            </div>
            <div>
                <strong>Guest:</strong> {{ $booking->guest }} orang
            </div>
            <div>
                <strong>Total Price:</strong> Rp {{ number_format($booking->total_price, 0, ',', '.') }}
            </div>
            <div>
                <strong>Status:</strong> {{ ucfirst($booking->status) }}
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('dashboard') }}" class="bg-primary text-white px-6 py-2 rounded-full">
                Kembali ke Dashboard
            </a>
        </div>

    </div>

</div>
@endsection