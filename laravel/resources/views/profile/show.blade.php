@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4 md:px-0">
    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            <p class="font-semibold">✓ {{ session('success') }}</p>
        </div>
    @endif

    <!-- Profile Card -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-primary to-green-600 h-32"></div>

        <div class="px-6 pb-6">
            <!-- Avatar & Basic Info -->
            <div class="flex flex-col md:flex-row md:items-end gap-6 -mt-16 mb-6">
                <div>
                    <img 
                        src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=1F7A63&color=fff' }}" 
                        alt="{{ auth()->user()->name }}"
                        class="w-32 h-32 rounded-full border-4 border-white shadow-lg object-cover">
                </div>
                <div class="flex-1">
                    <h1 class="text-3xl font-bold text-gray-800">{{ auth()->user()->name }}</h1>
                    <p class="text-gray-600">Member sejak {{ auth()->user()->created_at->format('d M Y') }}</p>
                </div>
                <a href="{{ route('profile.edit') }}" class="bg-primary text-white px-6 py-2 rounded-full hover:bg-green-700 transition">
                    Edit Profil
                </a>
            </div>

            <!-- Profile Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Email -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i data-lucide="mail" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="font-semibold text-gray-800">{{ auth()->user()->email }}</p>
                    </div>
                </div>

                <!-- Phone -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i data-lucide="phone" class="w-5 h-5 text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Nomor Telepon</p>
                        <p class="font-semibold text-gray-800">{{ auth()->user()->phone ?? 'Belum diisi' }}</p>
                    </div>
                </div>

                <!-- WhatsApp -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i data-lucide="message-circle" class="w-5 h-5 text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">WhatsApp</p>
                        <p class="font-semibold text-gray-800">
                            @if(auth()->user()->whatsapp_number)
                                <a href="https://wa.me/{{ str_replace(['+', ' ', '-', '(', ')'], '', auth()->user()->whatsapp_number) }}" target="_blank" class="text-green-600 hover:underline">
                                    {{ auth()->user()->whatsapp_number }}
                                </a>
                            @else
                                Belum diisi
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Role/Status -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <i data-lucide="badge" class="w-5 h-5 text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Status</p>
                        <p class="font-semibold text-gray-800 capitalize">
                            @if(auth()->user()->isAdmin())
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">Administrator</span>
                            @else
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm">Pengguna Biasa</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Address -->
            @if(auth()->user()->address)
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Alamat</p>
                    <p class="text-gray-800">{{ auth()->user()->address }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Change Password Button -->
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">Keamanan Akun</h3>
                <p class="text-sm text-gray-600">Ubah password Anda secara berkala untuk keamanan maksimal</p>
            </div>
            <button 
                onclick="document.getElementById('changePasswordModal').classList.remove('hidden')"
                class="bg-yellow-600 text-white px-6 py-2 rounded-full hover:bg-yellow-700 transition">
                Ubah Password
            </button>
        </div>
    </div>

    <!-- Riwayat Booking -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h2 class="text-xl font-bold text-gray-800">Riwayat Booking</h2>
        </div>

        @if($bookings->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Villa</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Tanggal Booking</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Total Malam</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Total Harga</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        @if($booking->villa->thumbnail)
                                            <img src="{{ asset('storage/' . $booking->villa->thumbnail) }}" alt="{{ $booking->villa->name }}" class="w-12 h-12 rounded object-cover">
                                        @endif
                                        <div>
                                            <p class="font-semibold text-gray-800">{{ $booking->villa->name }}</p>
                                            <p class="text-xs text-gray-600">{{ $booking->villa->location ?? 'Puncak' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-800">
                                    {{ $booking->checkin_date->format('d M Y') }} - {{ $booking->checkout_date->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-800 text-center">
                                    {{ $booking->getNights() }} malam
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-800">
                                    Rp {{ number_format($booking->total_price, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($booking->status === 'pending')
                                        <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">Menunggu Konfirmasi</span>
                                    @elseif($booking->status === 'approved')
                                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold">Disetujui</span>
                                    @elseif($booking->status === 'paid')
                                        <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-xs font-semibold">Menunggu Verifikasi</span>
                                    @elseif($booking->status === 'confirmed')
                                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">Dikonfirmasi</span>
                                    @elseif($booking->status === 'rejected')
                                        <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-semibold">Ditolak</span>
                                    @else
                                        <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs font-semibold">{{ ucfirst($booking->status) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <a href="{{ route('bookings.show', $booking->id) }}" class="text-primary hover:underline font-semibold">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-10 text-center">
                <i data-lucide="inbox" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                <p class="text-gray-600 text-lg">Belum ada riwayat booking</p>
                <a href="/villas" class="text-primary hover:underline mt-2 inline-block">Jelajahi villa kami</a>
            </div>
        @endif
    </div>
</div>

<!-- Change Password Modal -->
<div id="changePasswordModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-lg w-full max-w-md">
        <!-- Header -->
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">Ubah Password</h2>
            <button 
                onclick="document.getElementById('changePasswordModal').classList.add('hidden')"
                class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>

        <!-- Form -->
        <form action="{{ route('profile.update-password') }}" method="POST" class="p-6 space-y-4">
            @csrf

            <!-- Current Password -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Password Saat Ini</label>
                <input 
                    type="password" 
                    name="current_password" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                    required>
                @error('current_password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- New Password -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Password Baru</label>
                <input 
                    type="password" 
                    name="password" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                    required>
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password</label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"
                    required>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-4">
                <button 
                    type="button"
                    onclick="document.getElementById('changePasswordModal').classList.add('hidden')"
                    class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Batal
                </button>
                <button 
                    type="submit" 
                    class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:bg-green-700 transition font-semibold">
                    Ubah Password
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Init Lucide Icons -->
<script>
    lucide.createIcons();
</script>
@endsection
