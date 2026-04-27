@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10 print:py-4">

    <div class="bg-white rounded-2xl shadow-lg p-8 print:shadow-none print:p-6">

        <!-- Invoice Header -->
        <div class="flex justify-between items-start mb-8 print:mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">INVOICE</h1>
                <p class="text-gray-600">Booking #{{ $booking->id }}</p>
                <p class="text-sm text-gray-500">Invoice #: {{ App\Http\Controllers\InvoiceController::generateInvoiceNumber($booking) }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600">Date Issued:</p>
                <p class="font-semibold">{{ now()->format('d M Y') }}</p>
                @if($booking->approved_at)
                <p class="text-sm text-gray-600 mt-2">Payment Confirmed:</p>
                <p class="font-semibold">{{ $booking->approved_at->format('d M Y H:i') }}</p>
                @endif
            </div>
        </div>

        <!-- Company & User Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="border rounded-lg p-4">
                <h3 class="font-semibold text-gray-700 mb-3">Billed To:</h3>
                <div class="space-y-1">
                    <p class="font-medium">{{ $booking->user->name }}</p>
                    <p class="text-sm text-gray-600">{{ $booking->user->email }}</p>
                    @if($booking->user->phone)
                    <p class="text-sm text-gray-600">{{ $booking->user->phone }}</p>
                    @endif
                </div>
            </div>
            <div class="border rounded-lg p-4">
                <h3 class="font-semibold text-gray-700 mb-3">Property Details:</h3>
                <div class="space-y-1">
                    <p class="font-medium">{{ $booking->villa->name }}</p>
                    @if($booking->villa->address)
                    <p class="text-sm text-gray-600">{{ Str::limit($booking->villa->address, 50) }}</p>
                    @endif
                    <p class="text-sm text-gray-600">Guests: {{ $booking->guest }} persons</p>
                </div>
            </div>
        </div>

        <!-- Booking Details Table -->
        <div class="mb-8">
            <h3 class="font-semibold text-gray-700 mb-3">Booking Details</h3>
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="border border-gray-300 px-4 py-2 text-left">Description</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Dates</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Nights</th>
                        <th class="border border-gray-300 px-4 py-2 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-gray-300 px-4 py-3">{{ $booking->villa->name }}</td>
                        <td class="border border-gray-300 px-4 py-3 text-center">
                            {{ $booking->check_in->format('d M Y') }} -<br>
                            {{ $booking->check_out->format('d M Y') }}
                        </td>
                        <td class="border border-gray-300 px-4 py-3 text-center">{{ $booking->getNights() }}</td>
                        <td class="border border-gray-300 px-4 py-3 text-right">Rp {{ number_format($booking->total_price, 0, ',', '.') }}</td>
                    </tr>
                    @if($booking->markup_amount && $booking->markup_amount > 0)
                    <tr>
                        <td class="border border-gray-300 px-4 py-2" colspan="3">Service Fee</td>
                        <td class="border border-gray-300 px-4 py-2 text-right">Rp {{ number_format($booking->markup_amount, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="bg-gray-100 font-semibold">
                        <td class="border border-gray-300 px-4 py-3" colspan="3">Total Amount</td>
                        <td class="border border-gray-300 px-4 py-3 text-right text-lg">
                            Rp {{ number_format($booking->getTotalWithMarkup(), 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Payment Information -->
        @if($booking->payment)
        <div class="mb-8">
            <h3 class="font-semibold text-gray-700 mb-3">Payment Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Payment Method:</p>
                    <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $booking->payment->payment_method)) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Payment Status:</p>
                    <p class="font-medium text-green-600">✓ Confirmed</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Check-in Information -->
        <div class="border-t pt-6">
            <h3 class="font-semibold text-gray-700 mb-4">Check-in Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($booking->villa->contact_pengelola_villa)
                <div>
                    <p class="font-medium mb-2">📞 Contact Person</p>
                    <p class="text-gray-700">{{ $booking->villa->contact_pengelola_villa }}</p>
                </div>
                @else
                <div>
                    <p class="font-medium mb-2">📞 Contact Person</p>
                    <p class="text-gray-500 italic">Contact information will be provided</p>
                </div>
                @endif

                @if($booking->villa->google_maps_link)
                <div>
                    <p class="font-medium mb-2">🗺️ Location</p>
                    <a href="{{ $booking->villa->google_maps_link }}"
                       target="_blank"
                       class="text-blue-600 hover:text-blue-800 underline inline-flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        View on Google Maps
                    </a>
                </div>
                @else
                <div>
                    <p class="font-medium mb-2">🗺️ Location</p>
                    <p class="text-gray-500 italic">Location details will be provided</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 pt-6 border-t text-center">
            <p class="text-sm text-gray-600 mb-4">Thank you for your booking!</p>
            <div class="flex justify-center space-x-4 print:hidden">
                <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    🖨️ Print Invoice
                </button>
                <a href="{{ route('dashboard') }}" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    ← Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Print-specific footer -->
        <div class="hidden print:block mt-8 pt-6 border-t text-center text-xs text-gray-500">
            <p>This is a computer-generated invoice. No signature required.</p>
            <p>Generated on {{ now()->format('d M Y H:i:s') }}</p>
        </div>

    </div>

</div>
@endsection