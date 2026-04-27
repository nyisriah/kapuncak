@extends('layouts.app')

@section('content')
@if(session('success'))
    <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<div class="max-w-6xl mx-auto py-10">

    <h1 class="text-2xl font-bold mb-6">
        Dashboard Saya
    </h1>

    <div class="bg-white rounded-2xl shadow p-6">

        <h2 class="text-lg font-semibold mb-4">
            Riwayat Booking
        </h2>

        @forelse($bookings as $booking)
            <div class="border-b py-4">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1">
                        <p class="font-semibold text-lg">
                            {{ $booking->villa->name ?? 'Villa' }}
                        </p>
                        <p class="text-sm text-gray-600 mb-1">
                            {{ $booking->checkin_date->format('d M Y') }} - {{ $booking->checkout_date->format('d M Y') }}
                            <span class="text-gray-400">({{ $booking->getNights() }} nights)</span>
                        </p>
                        <p class="text-sm text-gray-600">
                            {{ $booking->guest }} guests • Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                        </p>
                    </div>
                    
                    <div class="text-right ml-4">
                        <!-- ENHANCED STATUS DISPLAY -->
                        @if($booking->status == 'pending')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                Menunggu Konfirmasi
                            </span>

                        @elseif($booking->status == 'approved')
                            @if($booking->isExpired())
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Booking Expired
                                </span>
                            @else
                                <div class="space-y-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Approved
                                    </span>
                                    <div class="text-xs text-gray-600">
                                        <p>Deadline: {{ $booking->getRemainingTime() }}</p>
                                    </div>
                                    <a href="{{ route('payments.create', $booking->id) }}"
                                       class="inline-block bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors">
                                        Bayar Sekarang
                                    </a>
                                </div>
                            @endif

                        @elseif($booking->status == 'paid')
                            @if($booking->payment && $booking->payment->status == 'pending')
                                <div class="space-y-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Menunggu Verifikasi
                                    </span>
                                    @if($booking->payment->proof)
                                    <p class="text-xs text-gray-600">Payment uploaded - awaiting admin verification</p>
                                    @endif
                                </div>
                            @elseif($booking->payment && $booking->payment->status == 'rejected')
                                <div class="space-y-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Payment Rejected
                                    </span>
                                    @if($booking->payment->rejection_reason)
                                    <p class="text-xs text-red-600 max-w-xs">{{ $booking->payment->rejection_reason }}</p>
                                    @endif
                                    <a href="{{ route('payments.create', $booking->id) }}"
                                       class="inline-block bg-orange-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-orange-700 transition-colors">
                                        Upload Ulang
                                    </a>
                                </div>
                            @endif

                        @elseif($booking->status == 'confirmed')
                            <div class="space-y-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Confirmed
                                </span>
                                <div class="space-x-2">
                                    <a href="{{ route('invoice.show', $booking->id) }}"
                                       class="inline-block bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 transition-colors">
                                        Download Invoice
                                    </a>
                                </div>
                            </div>

                        @elseif($booking->status == 'rejected')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Booking Ditolak
                            </span>

                        @elseif($booking->status == 'cancelled')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Booking Dibatalkan
                            </span>
                        @endif
                    </div>
                </div>

                <!-- ADDITIONAL INFO FOR SPECIFIC STATUSES -->
                @if($booking->status == 'approved' && $booking->payment_deadline && !$booking->isExpired())
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm">
                        <p class="text-blue-800">
                            <strong>Payment Deadline:</strong> {{ $booking->payment_deadline->format('d M Y H:i') }}
                        </p>
                        <p class="text-blue-600 text-xs mt-1">
                            Please complete payment before deadline to avoid automatic cancellation.
                        </p>
                    </div>
                @endif

                @if($booking->status == 'approved' && $booking->isExpired())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm">
                        <p class="text-red-800">
                            <strong>Booking Expired:</strong> Payment deadline was {{ $booking->payment_deadline->format('d M Y H:i') }}
                        </p>
                        <p class="text-red-600 text-xs mt-1">
                            This booking has been automatically cancelled. Please make a new booking.
                        </p>
                    </div>
                @endif

                @if($booking->payment && $booking->payment->status == 'rejected' && $booking->payment->rejection_reason)
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-3 text-sm">
                        <p class="text-orange-800">
                            <strong>Alasan Penolakan:</strong> {{ $booking->payment->rejection_reason }}
                        </p>
                        <p class="text-orange-600 text-xs mt-1">
                            Please upload payment proof again with correct information.
                        </p>
                    </div>
                @endif
            </div>
        @empty
            <p class="text-gray-500">
                Belum ada booking
            </p>
        @endforelse

    </div>

</div>
@endsection