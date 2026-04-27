@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-10 px-4">
    
    <div class="bg-yellow-50 p-6 rounded-3xl mb-6 border border-yellow-200">
        <h3 class="font-bold text-lg mb-4 text-yellow-800">💳 Instruksi Pembayaran</h3>
        <div class="space-y-4">
            <div>
                <p class="text-sm font-semibold text-gray-700">Transfer Bank (BCA)</p>
                <p class="text-xl font-bold text-green-700">7361140200</p>
                <p class="text-sm font-medium">Atas Nama: Irpan Nurdin</p>
            </div>
            <hr class="border-yellow-200">
            <div>
                <p class="text-sm font-semibold text-gray-700">DANA</p>
                <p class="text-xl font-bold text-green-700">0815-993-8353</p>
                <p class="text-sm font-medium">Atas Nama: Irpan Nurdin</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-xl border p-8">
        <h1 class="text-2xl font-bold mb-4">Upload Bukti Pembayaran</h1>

        <div class="mb-6 p-4 bg-gray-50 rounded-2xl border border-gray-100">
            <h2 class="font-semibold">{{ $booking->villa->name }}</h2>
            <p class="text-sm text-gray-600">
                Total Tagihan: <span class="font-bold text-green-600">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</span>
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-600 rounded-2xl text-sm">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('payments.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <input type="hidden" name="booking_id" value="{{ $booking->id }}">

            <div>
                <label for="proof" class="block text-sm font-medium text-gray-700 mb-2">Pilih File Bukti (JPG, PNG, max 2MB)</label>
                <input type="file" name="proof" id="proof" accept="image/*" required 
                    class="block w-full border border-gray-300 rounded-xl p-2 text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-full file:border-0
                    file:text-sm file:font-semibold
                    file:bg-green-50 file:text-green-700
                    hover:file:bg-green-100">
            </div>

            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-full transition shadow-lg">
                Kirim Bukti Pembayaran
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600 text-sm">
                Batal dan Kembali
            </a>
        </div>
    </div>
</div>
@endsection