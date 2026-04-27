@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-10">

    <div class="bg-white rounded-2xl shadow p-6">

        <h1 class="text-2xl font-bold mb-6">
            Pembayaran Booking
        </h1>

        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-2">
                {{ $payment->booking->villa->name }}
            </h2>
            <p class="text-gray-600">
                Check-in: {{ $payment->booking->checkin_date->format('d M Y') }}<br>
                Check-out: {{ $payment->booking->checkout_date->format('d M Y') }}<br>
                Total: Rp {{ number_format($payment->amount, 0, ',', '.') }}
            </p>
        </div>

        @if($payment->proof)
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2">Bukti Pembayaran</h3>
                <img src="{{ asset('storage/' . $payment->proof) }}" alt="Bukti Pembayaran" class="max-w-full h-auto rounded">
                <p class="text-sm text-gray-500 mt-2">
                    Status: {{ ucfirst($payment->status) }}
                </p>
            </div>
        @else
            <form action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="hidden" name="booking_id" value="{{ $payment->booking_id }}">

                <div>
                    <label for="proof" class="block text-sm font-medium text-gray-700">Upload Bukti Pembayaran</label>
                    <input type="file" name="proof" id="proof" accept="image/*" required class="mt-1 block w-full">
                </div>

                <button type="submit" class="bg-primary text-white px-6 py-2 rounded-full">
                    Upload Bukti Pembayaran
                </button>
            </form>
        @endif

        <div class="mt-6">
            <a href="{{ route('dashboard') }}" class="text-primary underline">Kembali ke Dashboard</a>
        </div>

    </div>

</div>
@endsection